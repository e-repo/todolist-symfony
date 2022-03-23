<?php

declare(strict_types=1);

namespace App\Service\JsonApi\ResponseBuilder\Params;

abstract class AbstractParams
{
    abstract public function getProperties(): array;
}