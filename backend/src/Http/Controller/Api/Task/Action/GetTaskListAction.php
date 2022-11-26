<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\Task\Action;

use App\Domain\Todos\Task\Read\Filter\TaskListFilter;
use App\Http\Payload\Api\Task\TaskListPayload;
use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use App\Http\Service\BaseActionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetTaskListAction implements BaseActionInterface
{
    /**
     * @param TaskListPayload|BasePayloadInterface $payload
     * @return JsonResponse
     */
    public function handle(BasePayloadInterface $payload): JsonResponse
    {
        $filter = (new TaskListFilter($payload->userUuid, $payload->status))
            ->setPublishedStartDate($payload->publishedStartDate)
            ->setPublishedEndDate($payload->publishedEndDate)
            ->setPage($payload->page)
            ->setPerPage($payload->perPage)
            ->setDirection($payload->direction)
            ->setSort($payload->sort);

        dd($filter);
    }
}
