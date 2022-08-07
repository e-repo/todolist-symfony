<?php

namespace App\Http\Service\JsonApi\HttpException;

use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;

interface JsonApiHttpExceptionInterface
{
    public function getResponseDataBuilder(): ResponseDataBuilder;
}