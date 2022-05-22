<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\User;

use App\Domain\User\Entity\User\User;
use App\Domain\User\Read\Filter\Filter;
use App\Domain\User\Read\UserFetcher;
use App\Http\Payload\Api\User\UserListPayload;
use App\Http\Service\JsonApi\JsonApiHelper;
use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
use App\Infrastructure\Security\RolesHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/api", name="user")
 */
class UserController extends AbstractController
{
    private const PER_PAGE = 15;
    private UserFetcher $fetcher;
    private JsonApiHelper $apiHelper;
    private UrlGeneratorInterface $urlGenerator;
    private RolesHelper $rolesHelper;

    /**
     * @param UserFetcher $fetcher
     * @param JsonApiHelper $apiHelper
     * @param UrlGeneratorInterface $urlGenerator
     * @param RolesHelper $rolesHelper
     */
    public function __construct(
        UserFetcher $fetcher,
        JsonApiHelper $apiHelper,
        UrlGeneratorInterface $urlGenerator,
        RolesHelper $rolesHelper
    )
    {
        $this->fetcher = $fetcher;
        $this->apiHelper = $apiHelper;
        $this->urlGenerator = $urlGenerator;
        $this->rolesHelper = $rolesHelper;
    }

    /**
     * @Route("/v1/user/list", name=".list", methods={"GET"})
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
     * @Route("/v1/user/role/list", name="_role.list", methods={"GET"})
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
     * @Route("/v1/user/status/list", name="_status.list", methods={"GET"})
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
     * @Route("/v1/user/{id}", name="_profile", methods={"GET"})
     * @param User $user
     * @return JsonResponse
     */
    public function getUser(User $user): JsonResponse
    {
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
            ->setDataAttribute('createdAt', $user->getDate()->format('d.m.Y H:i:s'))
            ->setDataAttribute('role', $user->getRole()->name())
            ->setDataAttribute('status', $user->getStatus());

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
    }
}