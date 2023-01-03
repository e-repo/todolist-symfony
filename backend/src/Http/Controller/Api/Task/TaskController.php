<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\Task;

use App\Domain\Todos\Task\Query\GetTask\GetTaskHandler;
use App\Domain\Todos\Task\Query\GetTask\GetTaskQuery;
use App\Http\Controller\Api\Task\Action\CreateTaskAction;
use App\Http\Controller\Api\Task\Action\GetTaskListAction;
use App\Http\Controller\Api\Task\Action\UpdateTaskAction;
use App\Http\Payload\Api\Task\TaskCreatePayload;
use App\Http\Payload\Api\Task\TaskListPayload;
use App\Http\Payload\Api\Task\TaskUpdatePayload;
use App\Http\Service\JsonApi\JsonApiHelper;
use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * @param Route
 * @Route("/api/v1/task", name="task")
 */
class TaskController extends AbstractController
{
    private JsonApiHelper $apiHelper;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        JsonApiHelper $apiHelper,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->apiHelper = $apiHelper;
        $this->urlGenerator = $urlGenerator;
    }

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

    /**
     * @Route("/{uuid}", name=".info", methods={"GET"})
     * @param string $uuid
     * @param GetTaskHandler $getTaskHandler
     * @return JsonResponse
     */
    public function getTaskByUuid(
        string $uuid,
        GetTaskHandler $getTaskHandler
    ): JsonResponse
    {
        $responseDataBuilder = ResponseDataBuilder::create();

        try {
            $taskPresenterDto = $getTaskHandler->handle(new GetTaskQuery($uuid));

            $selfLink = $this->urlGenerator->generate('task.info', ['uuid' => $uuid], UrlGeneratorInterface::ABSOLUTE_URL);

            $responseDataBuilder
                ->setLinksSelf($selfLink)
                ->setDataAttribute('uuid', $taskPresenterDto->getUuid())
                ->setDataAttribute('userUuid', $taskPresenterDto->getUserUuid())
                ->setDataAttribute('name', $taskPresenterDto->getName())
                ->setDataAttribute('description', $taskPresenterDto->getDescription())
                ->setDataAttribute('status', $taskPresenterDto->getStatus())
                ->setDataAttribute('date', $taskPresenterDto->getDate()->format('d.m.Y H:i:s'));

            return $this->apiHelper->createJsonResponse($responseDataBuilder);
        } catch (EntityNotFoundException $e) {
            $responseDataBuilder
                ->setErrorsTitle(Response::$statusTexts[Response::HTTP_NOT_FOUND])
                ->setErrorsDetail($e->getMessage())
                ->setErrorsStatus(Response::HTTP_NOT_FOUND);

            return $this->apiHelper->createJsonResponse($responseDataBuilder, Response::HTTP_NOT_FOUND);
        }  catch (\Exception $e) {
            $responseDataBuilder
                ->setErrorsTitle(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY])
                ->setErrorsDetail($e->getMessage())
                ->setErrorsStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return $this->apiHelper->createJsonResponse($responseDataBuilder, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @Route("/create", name=".create", methods={"POST"})
     * @param TaskCreatePayload $payload
     * @param CreateTaskAction $createTaskAction
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function createTask(
        TaskCreatePayload $payload,
        CreateTaskAction $createTaskAction
    ): JsonResponse
    {
        return $createTaskAction->handle($payload);
    }

    /**
     * @Route("/update", name=".update", methods={"PUT"})
     * @param TaskUpdatePayload $payload
     * @param UpdateTaskAction $updateTaskAction
     * @return JsonResponse
     */
    public function updateTask(
        TaskUpdatePayload $payload,
        UpdateTaskAction $updateTaskAction
    ): JsonResponse
    {
        return $updateTaskAction->handle($payload);
    }
}
