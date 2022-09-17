<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\User;

use App\Domain\Auth\User\UseCase;
use App\Domain\Auth\User\Entity\User\Id;
use App\Domain\Auth\User\Entity\User\User;
use App\Domain\Auth\User\Read\Filter\Filter;
use App\Domain\Auth\User\Read\UserFetcher;
use App\Domain\Auth\User\Repository\ImageRepository;
use App\Domain\Auth\User\Repository\UserRepository;
use App\Domain\Auth\User\Service\NewEmailConfirmTokenizer;
use App\Http\Payload\Api\User\ChangeEmailPayload;
use App\Http\Payload\Api\User\ChangeNamePayload;
use App\Http\Payload\Api\User\UserListPayload;
use App\Http\Service\JsonApi\JsonApiHelper;
use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
use App\Infrastructure\Security\RolesHelper;
use App\Infrastructure\Upload\UploadHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypesInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/user", name="user")
 */
class UserController extends AbstractController
{
    private const PER_PAGE = 15;
    private UserFetcher $fetcher;
    private JsonApiHelper $apiHelper;
    private UrlGeneratorInterface $urlGenerator;
    private RolesHelper $rolesHelper;
    private UserRepository $userRepository;
    private ValidatorInterface $validator;
    private MimeTypesInterface $mimeTypes;

    /**
     * @param UserFetcher $fetcher
     * @param UserRepository $userRepository
     * @param JsonApiHelper $apiHelper
     * @param UrlGeneratorInterface $urlGenerator
     * @param RolesHelper $rolesHelper
     * @param ValidatorInterface $validator
     * @param MimeTypesInterface $mimeTypes
     */
    public function __construct(
        UserFetcher $fetcher,
        UserRepository $userRepository,
        JsonApiHelper $apiHelper,
        UrlGeneratorInterface $urlGenerator,
        RolesHelper $rolesHelper,
        ValidatorInterface $validator,
        MimeTypesInterface $mimeTypes
    )
    {
        $this->fetcher = $fetcher;
        $this->apiHelper = $apiHelper;
        $this->urlGenerator = $urlGenerator;
        $this->rolesHelper = $rolesHelper;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->mimeTypes = $mimeTypes;
    }

    /**
     * @Route("/list", name=".list", methods={"GET"})
     * @param UserListPayload $payload
     * @return JsonResponse
     */
    public function getUserList(UserListPayload $payload): JsonResponse
    {
        $filter = (new Filter())
            ->setId($payload->id)
            ->setName($payload->name)
            ->setEmail($payload->email)
            ->setStatus($payload->status)
            ->setRole($payload->role);

        $pagination = $this->fetcher->all(
            $filter,
            $payload->page ?? 1,
            self::PER_PAGE,
            $payload->sort ?? 'date',
            $payload->direction ?? 'desk'
        );

        $total = $pagination->getTotalItemCount();
        $numberOfPages = (int)\ceil($total/self::PER_PAGE);

        $linkSelf = $this->urlGenerator->generate(
            'user.list',
            ['page' => $pagination->getCurrentPageNumber()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $linkFirst = $this->urlGenerator->generate(
            'user.list',
            ['page' => 1],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $responseDataBuilder = ResponseDataBuilder::create()
            ->setLinksSelf($linkSelf)
            ->setLinksFirst($linkFirst)
            ->setMetaAttribute('totalPage', $numberOfPages)
            ->setMetaAttribute('currentPage', $pagination->getCurrentPageNumber())
            ->setMetaAttribute('perPage', self::PER_PAGE)
            ->setDataAllParams($pagination->getItems());

        if ($numberOfPages > 1) {
            $linkLast = $this->urlGenerator->generate(
                'user.list',
                ['page' => $numberOfPages],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $responseDataBuilder
                ->setLinksLast($linkLast);
        }

        if ($pagination->getCurrentPageNumber() < $numberOfPages) {
            $linkNext = $this->urlGenerator->generate(
                'user.list',
                ['page' => $pagination->getCurrentPageNumber() + 1],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $responseDataBuilder
                ->setLinksNext($linkNext);
        }

        if ($pagination->getCurrentPageNumber() > 1) {
            $linkPrev = $this->urlGenerator->generate(
                'user.list',
                ['page' => $pagination->getCurrentPageNumber() - 1],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $responseDataBuilder
                ->setLinksPrev($linkPrev);
        }

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
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
            ['id' => $user->getId()->getValue()],
            UrlGeneratorInterface::ABSOLUTE_URL
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
                        'path' => $uploadHelper->getPublicPath($userActiveImage->getFilePath()),
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
     * @param UseCase\Email\Request\Handler $handler
     * @param NewEmailConfirmTokenizer $tokenizer
     * @return JsonResponse
     */
    public function changeEmail(
        ChangeEmailPayload $payload,
        UseCase\Email\Request\Handler $handler,
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

        $command = (new UseCase\Email\Request\Command())
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
     * @param UseCase\Image\Attach\Handler $handler
     * @param ImageRepository $imageRepository
     * @param UploadHelper $uploadHelper
     * @return JsonResponse
     */
    public function uploadProfileImage(
        Request                                            $request,
        UseCase\Image\Attach\Handler $handler,
        ImageRepository                                    $imageRepository,
        UploadHelper                                       $uploadHelper
    ): JsonResponse
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('image');

        $violations = $this->validator->validate($uploadedFile, [
            new NotBlank(),
            new File([
                'maxSize' => '2M',
                'mimeTypes' => \array_merge($this->mimeTypes->getMimeTypes('jpeg'), $this->mimeTypes->getMimeTypes('png'))
            ])
        ]);

        if ($violations->count() > 0) {
            $responseDataBuilder = ResponseDataBuilder::create();

            /** @var ConstraintViolation $violation */
            foreach ($violations as $violation) {
                $responseDataBuilder
                    ->setErrorsTitle('Image validation error.')
                    ->setErrorsDetail($violation->getMessage());
            }

            return $this->apiHelper->createJsonResponse($responseDataBuilder, Response::HTTP_BAD_REQUEST);
        }

        $command = new UseCase\Image\Attach\Command($uploadedFile, $request->get('uuid'));

        try {
            $handler->handle($command);
        } catch (\Exception $e) {
            $responseDataBuilder = ResponseDataBuilder::create()
                ->setErrorsTitle('Image save error.')
                ->setErrorsDetail($e->getMessage());

            return $this->apiHelper->createJsonResponse($responseDataBuilder, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $linkSelf = $this->urlGenerator->generate(
            'user_profile.change_name',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $activeImage = $imageRepository->findActiveImageByUserId(new Id($request->get('uuid')));

        $responseDataBuilder = ResponseDataBuilder::create()
            ->setLinksSelf($linkSelf)
            ->setDataType('Image save successfully.')
            ->setDataAttribute('imagePath', $uploadHelper->getPublicPath($activeImage->getFilePath()));

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
    }
}