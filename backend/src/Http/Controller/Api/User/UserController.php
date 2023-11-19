<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\User;

use App\Domain\Auth\User\Entity\User\User;
use App\Domain\Auth\User\Error\Image\ImageNotBelongsToUserException;
use App\Domain\Auth\User\Error\Image\UserImageNotFoundException;
use App\Domain\Auth\User\Repository\UserRepository;
use App\Domain\Auth\User\Service\NewEmailConfirmTokenizer;
use App\Domain\Auth\User\UseCase;
use App\Http\Controller\Api\User\Action\GetUserImageListAction;
use App\Http\Controller\Api\User\Action\GetUserListAction;
use App\Http\Controller\Api\User\Action\UploadUserImageAction;
use App\Http\Controller\Api\User\Response\ImageResponseDto;
use App\Http\Payload\Api\User\ChangeEmailPayload;
use App\Http\Payload\Api\User\ChangeNamePayload;
use App\Http\Payload\Api\User\UserImageActivatePayload;
use App\Http\Payload\Api\User\UserImageListPayload;
use App\Http\Payload\Api\User\UserImagePayload;
use App\Http\Payload\Api\User\UserListPayload;
use App\Http\Service\JsonApi\JsonApiHelper;
use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
use App\Infrastructure\Security\RolesHelper;
use App\Infrastructure\Upload\UploadHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/api/v1/user", name="user")
 */
class UserController extends AbstractController
{
    private JsonApiHelper $apiHelper;
    private NormalizerInterface $normalizer;
    private UrlGeneratorInterface $urlGenerator;
    private RolesHelper $rolesHelper;
    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     * @param NormalizerInterface $normalizer
     * @param JsonApiHelper $apiHelper
     * @param UrlGeneratorInterface $urlGenerator
     * @param RolesHelper $rolesHelper
     */
    public function __construct(
        UserRepository $userRepository,
        NormalizerInterface $normalizer,
        JsonApiHelper $apiHelper,
        UrlGeneratorInterface $urlGenerator,
        RolesHelper $rolesHelper
    )
    {
        $this->apiHelper = $apiHelper;
        $this->urlGenerator = $urlGenerator;
        $this->rolesHelper = $rolesHelper;
        $this->userRepository = $userRepository;
        $this->normalizer = $normalizer;
    }

    /**
     * @Route("/list", name=".list", methods={"GET"})
     * @param UserListPayload $payload
     * @param GetUserListAction $listAction
     * @return JsonResponse
     */
    public function getUserList(
        UserListPayload $payload,
        GetUserListAction $listAction
    ): JsonResponse
    {
        return $listAction->handle($payload);
    }

    /**
     * @Route("/role/list", name="_role.list", methods={"GET"})
     * @return JsonResponse
     */
    public function getUserRoles(): JsonResponse
    {
        $linkSelf = $this->urlGenerator->generate(
            'user_role.list',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $responseDataBuilder = ResponseDataBuilder::create()
            ->setLinksSelf($linkSelf)
            ->setDataType('User roles')
            ->setDataAttribute('roles', $this->rolesHelper->getRoles());

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
    }

    /**
     * @Route("/status/list", name="_status.list", methods={"GET"})
     * @return JsonResponse
     */
    public function getUserStatuses(): JsonResponse
    {
        $linkSelf = $this->urlGenerator->generate(
            'user_status.list',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $responseDataBuilder = ResponseDataBuilder::create()
            ->setLinksSelf($linkSelf)
            ->setDataType('User statuses')
            ->setDataAttribute('statuses', User::allStatuses());

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
    }

    /**
     * @Route("/{id}/profile", name="_profile", methods={"GET"})
     * @param string $id
     * @param UploadHelper $uploadHelper
     * @return JsonResponse
     */
    public function getUserProfile(string $id, UploadHelper $uploadHelper): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (null === $user) {
            $responseDataBuilder = ResponseDataBuilder::create()
                ->setErrorsStatus(400)
                ->setErrorsDetail('User not found.');

            return $this->apiHelper->createJsonResponse($responseDataBuilder, Response::HTTP_NOT_FOUND);
        }

        $linkSelf = $this->urlGenerator->generate(
            'user_profile',
            ['id' => $user->getId()->getValue()]
        );

        $responseDataBuilder = ResponseDataBuilder::create()
            ->setLinksSelf($linkSelf)
            ->setDataType('User profile')
            ->setDataUuid($user->getId()->getValue())
            ->setDataAttribute('name', $user->getName()->getFull())
            ->setDataAttribute('email', $user->getEmail()->getValue())
            ->setDataAttribute('createdAt', $user->getDate()->getTimestamp())
            ->setDataAttribute('role', $user->getRole()->name())
            ->setDataAttribute('status', $user->getStatus());

        if ($userActiveImage = $user->getActiveImage()) {
            $responseDataBuilder
                ->setDataRelationships([
                    'image' => [
                        'path' => $uploadHelper->getRelativePath($userActiveImage->getFilePath()),
                        'data' => [
                            'uuid' => $userActiveImage->getId(),
                            'fileName' => $userActiveImage->getFilename(),
                            'mimeType' => $userActiveImage->getMimeType(),
                            'createdAt' => $userActiveImage->getCreatedAt()->getTimestamp()
                        ]
                    ]
                ]);
        }

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
    }

    /**
     * @Route("/change-email", name="_profile.change_email", methods={"PATCH"})
     * @param ChangeEmailPayload $payload
     * @param UseCase\Email\ChangeRequest\Handler $handler
     * @param NewEmailConfirmTokenizer $tokenizer
     * @return JsonResponse
     */
    public function changeEmail(
        ChangeEmailPayload $payload,
        UseCase\Email\ChangeRequest\Handler $handler,
        NewEmailConfirmTokenizer $tokenizer
    ): JsonResponse
    {
        $user = $this->userRepository->findById($payload->uuid);

        if (null === $user) {
            $responseDataBuilder = ResponseDataBuilder::create()
                ->setErrorsStatus(404)
                ->setErrorsDetail('User not found.');

            return $this->apiHelper->createJsonResponse($responseDataBuilder, Response::HTTP_NOT_FOUND);
        }

        $token = $tokenizer->generate();
        $confirmUrl = $this->generateUrl(
            'profile.email_confirm_page',
            [
                'token' => $token,
                'user_id' => $user->getId()->getValue()
            ]
        );

        $command = (new UseCase\Email\ChangeRequest\Command())
            ->setId($user->getId()->getValue())
            ->setNewEmail($payload->email)
            ->setToken($token)
            ->setConfirmUrl($this->getParameter('site_base_url') . $confirmUrl);

        $handler->handle($command);

        $linkSelf = $this->urlGenerator->generate(
            'user_profile.change_email',
            ['id' => $user->getId()->getValue()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $responseDataBuilder = ResponseDataBuilder::create()
            ->setLinksSelf($linkSelf)
            ->setDataType('User profile')
            ->setDataAttribute('message', 'Change email request sent successfully. Check your email.');

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
    }

    /**
     * @Route("/change-name", name="_profile.change_name", methods={"PUT"})
     * @param ChangeNamePayload $payload
     * @param UseCase\Name\Handler $handler
     * @return JsonResponse
     */
    public function changeName(
        ChangeNamePayload $payload,
        UseCase\Name\Handler $handler
    ): JsonResponse
    {
        $user = $this->userRepository->findById($payload->uuid);

        if (null === $user) {
            $responseDataBuilder = ResponseDataBuilder::create()
                ->setErrorsStatus(400)
                ->setErrorsDetail('User not found.');

            return $this->apiHelper->createJsonResponse($responseDataBuilder, Response::HTTP_NOT_FOUND);
        }

        $command = (new UseCase\Name\Command($payload->uuid))
            ->setFirst($payload->firstName)
            ->setLast($payload->lastName);

        $handler->handle($command);

        $linkSelf = $this->urlGenerator->generate(
            'user_profile.change_name',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $responseDataBuilder = ResponseDataBuilder::create()
            ->setLinksSelf($linkSelf)
            ->setDataType('User profile')
            ->setDataAttribute('name', $user->getName()->getFull())
            ->setDataAttribute('email', $user->getEmail()->getValue())
            ->setDataAttribute('createdAt', $user->getDate()->getTimestamp())
            ->setDataAttribute('role', $user->getRole()->name())
            ->setDataAttribute('status', $user->getStatus());

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
    }

    /**
     * @Route("/image-upload", name="_profile.image-upload", methods={"POST"})
     * @param Request $request
     * @param UploadUserImageAction $userImageAction
     * @return JsonResponse
     */
    public function uploadProfileImage(
        Request $request,
        UploadUserImageAction $userImageAction
    ): JsonResponse
    {
        return $userImageAction->handle(
            new UserImagePayload(
                $request->get('uuid'),
                $request->files->get('image')
            )
        );
    }

    /**
     * @Route("/image/list", name="_image.list", methods={"GET"})
     * @param UserImageListPayload $payload
     * @param GetUserImageListAction $imageListAction
     * @return JsonResponse
     */
    public function getUserImages(
        UserImageListPayload $payload,
        GetUserImageListAction $imageListAction
    ): JsonResponse
    {
        return $imageListAction->handle($payload);
    }

    /**
     * @Route("/image/set-active", name="_image.set-active", methods={"PATCH"})
     * @param UserImageActivatePayload $payload
     * @param UseCase\Image\Activate\Handler $handler
     * @param UploadHelper $uploadHelper
     * @return JsonResponse
     */
    public function setActiveImage(
        UserImageActivatePayload $payload,
        UseCase\Image\Activate\Handler $handler,
        UploadHelper $uploadHelper
    ): JsonResponse
    {
        $responseDataBuilder = ResponseDataBuilder::create();

        try {
            $imageDto = $handler->handle(new UseCase\Image\Activate\Command($payload->userUuid, $payload->imageUuid));

            $imageResponseDto = (new ImageResponseDto())
                ->setUuid($imageDto->getUuid())
                ->setFilename($imageDto->getFilename())
                ->setOriginalFilename($imageDto->getOriginFilename())
                ->setFilepath($uploadHelper->getRelativePath($imageDto->getFilepath()))
                ->setIsActive($imageDto->isActive());

            $linkSelf = $this->urlGenerator->generate(
                'user_image.set-active'
            );

            $responseDataBuilder
                ->setLinksSelf($linkSelf)
                ->setDataType('Changing active image for user')
                ->setDataAllParams($this->normalizer->normalize($imageResponseDto));
        } catch (UserImageNotFoundException $e) {
            $responseDataBuilder
                ->setErrorsTitle($e->getMessage())
                ->setErrorsDetail(sprintf('Image by id \'%s\'', $e->getParam('imageUuid')))
                ->setErrorsStatus(Response::HTTP_NOT_FOUND);

            return $this->apiHelper->createJsonResponse($responseDataBuilder, Response::HTTP_NOT_FOUND);
        } catch (ImageNotBelongsToUserException $e) {
            $responseDataBuilder
                ->setErrorsTitle($e->getMessage())
                ->setErrorsDetail(sprintf('Image does\'t belongs to user %s', $e->getUserUuid()))
                ->setErrorsStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return $this->apiHelper->createJsonResponse($responseDataBuilder, Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $e) {
            return $this->apiHelper->createJsonResponseFromError($e);
        }

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
    }
}
