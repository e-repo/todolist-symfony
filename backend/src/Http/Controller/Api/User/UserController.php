<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\User;

use App\Domain\Auth\Entity\User\User;
use App\Domain\Auth\Entity\User\UserRepository;
use App\Domain\Auth\Service\NewEmailConfirmTokenizer;
use App\Domain\Auth\UseCase\Email;
use App\Domain\Auth\Read\Filter\Filter;
use App\Domain\Auth\Read\UserFetcher;
use App\Http\Payload\Api\User\ChangeEmailPayload;
use App\Http\Payload\Api\User\ChangeNamePayload;
use App\Http\Payload\Api\User\UserListPayload;
use App\Http\Service\JsonApi\JsonApiHelper;
use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
use App\Infrastructure\Security\RolesHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    /**
     * @param UserFetcher $fetcher
     * @param UserRepository $userRepository
     * @param JsonApiHelper $apiHelper
     * @param UrlGeneratorInterface $urlGenerator
     * @param RolesHelper $rolesHelper
     */
    public function __construct(
        UserFetcher $fetcher,
        UserRepository $userRepository,
        JsonApiHelper $apiHelper,
        UrlGeneratorInterface $urlGenerator,
        RolesHelper $rolesHelper
    )
    {
        $this->fetcher = $fetcher;
        $this->apiHelper = $apiHelper;
        $this->urlGenerator = $urlGenerator;
        $this->rolesHelper = $rolesHelper;
        $this->userRepository = $userRepository;
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
     * @return JsonResponse
     */
    public function getUserProfile(string $id): JsonResponse
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
            ->setDataAttribute('name', $user->getName()->getFull())
            ->setDataAttribute('email', $user->getEmail()->getValue())
            ->setDataAttribute('createdAt', $user->getDate()->getTimestamp())
            ->setDataAttribute('role', $user->getRole()->name())
            ->setDataAttribute('status', $user->getStatus());

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
    }

    /**
     * @Route("/change-email", name="_profile.change_email", methods={"PATCH"})
     * @param ChangeEmailPayload $payload
     * @param Email\Request\Handler $handler
     * @param NewEmailConfirmTokenizer $tokenizer
     * @return JsonResponse
     */
    public function changeEmail(
        ChangeEmailPayload $payload,
        Email\Request\Handler $handler,
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

        $command = (new Email\Request\Command())
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
     * @Route("/change-name", name="_profile.change_name", methods={"PATCH"})
     * @param ChangeNamePayload $payload
     * @return JsonResponse
     */
    public function changeName(
        ChangeNamePayload $payload
    ): JsonResponse
    {
        dd($payload);
    }
}