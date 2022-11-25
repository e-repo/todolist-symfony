<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\User\Action;

use App\Domain\Auth\User\Read\Filter\ListFilter;
use App\Domain\Auth\User\Read\UserFetcher;
use App\Http\Payload\Api\User\UserListPayload;
use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use App\Http\Service\BaseActionInterface;
use App\Http\Service\JsonApi\JsonApiHelper;
use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GetUserListAction implements BaseActionInterface
{
    private const DEFAULT_PER_PAGE = 15;

    private UrlGeneratorInterface $urlGenerator;
    private UserFetcher $fetcher;
    private JsonApiHelper $apiHelper;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        UserFetcher $fetcher,
        JsonApiHelper $apiHelper
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->fetcher = $fetcher;
        $this->apiHelper = $apiHelper;
    }

    /**
     * @param BasePayloadInterface|UserListPayload $payload
     * @return JsonResponse
     */
    public function handle(BasePayloadInterface $payload): JsonResponse
    {
        $filter = (new ListFilter())
            ->setName($payload->name)
            ->setEmail($payload->email)
            ->setStatus($payload->status)
            ->setRole($payload->role);

        $pagination = $this->fetcher->all(
            $filter,
            $payload->page ?? 1,
            $payload->perPage ?? self::DEFAULT_PER_PAGE,
            $payload->sort ?? 'date',
            $payload->direction ?? 'desc'
        );

        $total = $pagination->getTotalItemCount();
        $numberOfPages = (int)\ceil($total/self::DEFAULT_PER_PAGE);

        $linkSelf = $this->urlGenerator->generate(
            'user.list',
            ['page' => $pagination->getCurrentPageNumber()]
        );

        $linkFirst = $this->urlGenerator->generate(
            'user.list',
            ['page' => 1]
        );

        $responseDataBuilder = ResponseDataBuilder::create()
            ->setLinksSelf($linkSelf)
            ->setLinksFirst($linkFirst)
            ->setMetaAttribute('totalPage', $numberOfPages)
            ->setMetaAttribute('currentPage', $pagination->getCurrentPageNumber())
            ->setMetaAttribute('perPage', self::DEFAULT_PER_PAGE)
            ->setDataAllParams($pagination->getItems());

        if ($numberOfPages > 1) {
            $linkLast = $this->urlGenerator->generate(
                'user.list',
                ['page' => $numberOfPages]
            );

            $responseDataBuilder
                ->setLinksLast($linkLast);
        }

        if ($pagination->getCurrentPageNumber() < $numberOfPages) {
            $linkNext = $this->urlGenerator->generate(
                'user.list',
                ['page' => $pagination->getCurrentPageNumber() + 1]
            );

            $responseDataBuilder
                ->setLinksNext($linkNext);
        }

        if ($pagination->getCurrentPageNumber() > 1) {
            $linkPrev = $this->urlGenerator->generate(
                'user.list',
                ['page' => $pagination->getCurrentPageNumber() - 1]
            );

            $responseDataBuilder
                ->setLinksPrev($linkPrev);
        }

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
    }
}
