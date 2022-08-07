<?php

declare(strict_types=1);

namespace App\Http\Service\JsonApi\HttpException;

use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonApiHttpException extends HttpException implements JsonApiHttpExceptionInterface
{
    private ResponseDataBuilder $responseDataBuilder;

    public function __construct(
        int $statusCode,
        ResponseDataBuilder $responseDataBuilder,
        \Throwable $previous = null,
        int $code = 0,
        array $headers = []
    )
    {
        $this->responseDataBuilder = $responseDataBuilder;
        parent::__construct($statusCode, null, $previous, $headers, $code);
    }

    public function getResponseDataBuilder(): ResponseDataBuilder
    {
        return $this->responseDataBuilder;
    }
}