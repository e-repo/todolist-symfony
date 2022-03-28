<?php

declare(strict_types=1);

namespace App\Http\Service\JsonApi\ResponseBuilder\Params;

abstract class AbstractParams
{
    abstract public function getProperties(): array;
}