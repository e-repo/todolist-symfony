<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\Task\Action;

use App\Domain\Todos\Task\Read\Filter\TaskFilter;
use App\Domain\Todos\Task\Read\TaskFetcher;
use App\Http\Payload\Api\Task\TaskListPayload;
use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use App\Http\Service\BaseActionInterface;
use App\Http\Service\JsonApi\JsonApiHelper;
use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GetTaskListAction implements BaseActionInterface
{
    private const DEFAULT_PER_PAGE = 12;

    private TaskFetcher $taskFetcher;
    private JsonApiHelper $apiHelper;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        TaskFetcher $taskFetcher,
        JsonApiHelper $apiHelper,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->taskFetcher = $taskFetcher;
        $this->apiHelper = $apiHelper;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param TaskListPayload|BasePayloadInterface $payload
     * @return JsonResponse
     */
    public function handle(BasePayloadInterface $payload): JsonResponse
    {
        $filter = (new TaskFilter($payload->userUuid))
            ->setStatus($payload->status)
            ->setDirection($payload->direction)
            ->setDate($payload->onDate);

        $pagination = $this->taskFetcher->allByFilter(
            $filter,
            $payload->page ?? 1,
            $payload->perPage ?? self::DEFAULT_PER_PAGE
        );

        $total = $pagination->getTotalItemCount();
        $numberOfPages = (int)\ceil($total/self::DEFAULT_PER_PAGE);

        $linkSelf = $this->urlGenerator->generate(
            'task.list',
            ['page' => $pagination->getCurrentPageNumber()]
        );

        $linkFirst = $this->urlGenerator->generate(
            'task.list',
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
                'task.list',
                ['page' => $numberOfPages]
            );

            $responseDataBuilder
                ->setLinksLast($linkLast);
        }

        if ($pagination->getCurrentPageNumber() < $numberOfPages) {
            $linkNext = $this->urlGenerator->generate(
                'task.list',
                ['page' => $pagination->getCurrentPageNumber() + 1]
            );

            $responseDataBuilder
                ->setLinksNext($linkNext);
        }

        if ($pagination->getCurrentPageNumber() > 1) {
            $linkPrev = $this->urlGenerator->generate(
                'task.list',
                ['page' => $pagination->getCurrentPageNumber() - 1]
            );

            $responseDataBuilder
                ->setLinksPrev($linkPrev);
        }

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
    }
}
