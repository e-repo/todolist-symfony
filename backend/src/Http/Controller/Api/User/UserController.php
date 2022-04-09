<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\User;

use App\Domain\User\Read\Filter\Filter;
use App\Domain\User\Read\UserFetcher;
use App\Http\Payload\Api\User\UserListPayload;
use App\Http\Service\JsonApi\JsonApiHelper;
use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
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

    /**
     * @param UserFetcher $fetcher
     * @param JsonApiHelper $apiHelper
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        UserFetcher $fetcher,
        JsonApiHelper $apiHelper,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->fetcher = $fetcher;
        $this->apiHelper = $apiHelper;
        $this->urlGenerator = $urlGenerator;
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
}