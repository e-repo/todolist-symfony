<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\Task\Action;

use App\Domain\Todos\Task\UseCase\Update\Command;
use App\Domain\Todos\Task\UseCase\Update\Handler;
use App\Http\Payload\Api\Task\TaskUpdatePayload;
use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use App\Http\Service\BaseActionInterface;
use App\Http\Service\JsonApi\JsonApiHelper;
use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UpdateTaskAction implements BaseActionInterface
{
    private Handler $updateTaskHandler;
    private JsonApiHelper $apiHelper;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        Handler $updateTaskHandler,
        UrlGeneratorInterface $urlGenerator,
        JsonApiHelper $apiHelper
    )
    {
        $this->updateTaskHandler = $updateTaskHandler;
        $this->apiHelper = $apiHelper;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param BasePayloadInterface|TaskUpdatePayload $payload
     * @return JsonResponse
     */
    public function handle(BasePayloadInterface $payload): JsonResponse
    {
        $responseDataBuilder = ResponseDataBuilder::create();

        try {
            $command = (new Command())
                ->setTaskUuid($payload->uuid)
                ->setName($payload->name)
                ->setDescription($payload->description);

            $this->updateTaskHandler->handle($command);

            $selfLink = $this->urlGenerator->generate('task.update', [], UrlGeneratorInterface::ABSOLUTE_URL);

            $responseDataBuilder
                ->setLinksSelf($selfLink)
                ->setDataType('Task updated');

            return $this->apiHelper->createJsonResponse($responseDataBuilder);
        } catch (EntityNotFoundException $e) {
            $responseDataBuilder
                ->setErrorsTitle('Task not found')
                ->setErrorsDetail($e->getMessage())
                ->setErrorsStatus(Response::HTTP_NOT_FOUND);

            return $this->apiHelper->createJsonResponse($responseDataBuilder, Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            $responseDataBuilder
                ->setErrorsTitle(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY])
                ->setErrorsDetail($e->getMessage())
                ->setErrorsStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            return $this->apiHelper->createJsonResponse($responseDataBuilder, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
