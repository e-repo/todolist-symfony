<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\Task;

use App\Http\Controller\Api\Task\Action\GetTaskListAction;
use App\Http\Payload\Api\Task\TaskListPayload;
use App\Http\Service\JsonApi\JsonApiHelper;
use App\Infrastructure\Security\RolesHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/api/v1/task", name="task")
 */
class TaskController extends AbstractController
{
    private JsonApiHelper $apiHelper;
    private UrlGeneratorInterface $urlGenerator;
    private RolesHelper $rolesHelper;

    public function __construct(
        JsonApiHelper $apiHelper,
        UrlGeneratorInterface $urlGenerator,
        RolesHelper $rolesHelper
    )
    {
        $this->apiHelper = $apiHelper;
        $this->urlGenerator = $urlGenerator;
        $this->rolesHelper = $rolesHelper;
    }

    public function getTaskList(
        TaskListPayload $payload,
        GetTaskListAction $listAction
    ): JsonResponse
    {
        return $listAction->handle($payload);
    }
}
