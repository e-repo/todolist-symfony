<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\Task\Action;

use App\Domain\Todos\AuthAdapter\Exception\UserNotFoundException;
use App\Domain\Todos\Task\UseCase\Create\Command;
use App\Domain\Todos\Task\UseCase\Create\Handler;
use App\Http\Payload\Api\Task\TaskCreatePayload;
use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use App\Http\Service\BaseActionInterface;
use App\Http\Service\JsonApi\JsonApiHelper;
use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CreateTaskAction implements BaseActionInterface
{
    private JsonApiHelper $apiHelper;
    private Handler $createHandler;
    private UrlGeneratorInterface $urlGenerator;
    private NormalizerInterface $normalizer;

    public function __construct(
        Handler               $createHandler,
        UrlGeneratorInterface $urlGenerator,
        NormalizerInterface   $normalizer,
        JsonApiHelper         $apiHelper
    )
    {
        $this->apiHelper = $apiHelper;
        $this->createHandler = $createHandler;
        $this->urlGenerator = $urlGenerator;
        $this->normalizer = $normalizer;
    }

    /**
     * @param BasePayloadInterface|TaskCreatePayload $payload
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function handle(BasePayloadInterface $payload): JsonResponse
    {
        $responseDataBuilder = ResponseDataBuilder::create();

        try {
            $command = (new Command())
                ->setUserUuid($payload->userUuid)
                ->setName($payload->name)
                ->setDescription($payload->description);

            $task = $this->createHandler->handle($command);

            $selfLink = $this->urlGenerator->generate('task.create', [], UrlGeneratorInterface::ABSOLUTE_URL);

            $responseDataBuilder
                ->setLinksSelf($selfLink)
                ->setDataType('Task created')
                ->setDataAttribute('uuid', $task->getUuid())
                ->setDataAttribute('userUuid', $task->getUserUuid())
                ->setDataAttribute('name', $task->getName())
                ->setDataAttribute('description', $task->getDescription())
                ->setDataAttribute('status', $task->getStatus())
                ->setDataAttribute('date', $task->getDate()->format('d.m.Y H:i:s'));

            return $this->apiHelper->createJsonResponse($responseDataBuilder);
        } catch (UserNotFoundException $e) {
            $responseDataBuilder
                ->setErrorsTitle('User not found')
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
