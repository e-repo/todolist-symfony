<?php

declare(strict_types=1);

namespace App\Http\Service\JsonApi;

use App\Http\Service\JsonApi\HttpException\JsonApiHttpExceptionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonApiExceptionListener
{
    private JsonApiHelper $apiHelper;

    public function __construct(
        JsonApiHelper $apiHelper
    )
    {
        $this->apiHelper = $apiHelper;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        /** @var HttpException $exception */
        $exception = $event->getThrowable();
        if ($exception instanceof JsonApiHttpExceptionInterface) {
            $event->setResponse(
                $this->apiHelper->createJsonResponse(
                    $exception->getResponseDataBuilder(),
                    $exception->getStatusCode(),
                    $exception->getHeaders()
                )
            );
        }
    }
}