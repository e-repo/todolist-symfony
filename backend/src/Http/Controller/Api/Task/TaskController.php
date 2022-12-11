<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\Task;

use App\Http\Controller\Api\Task\Action\GetTaskListAction;
use App\Http\Payload\Api\Task\TaskListPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @param Route
 * @Route("/api/v1/task", name="task")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/list", name=".list", methods={"GET"})
     * @param TaskListPayload $payload
     * @param GetTaskListAction $listAction
     * @return JsonResponse
     */
    public function getTaskList(
        TaskListPayload $payload,
        GetTaskListAction $listAction
    ): JsonResponse
    {
        return $listAction->handle($payload);
    }
}
