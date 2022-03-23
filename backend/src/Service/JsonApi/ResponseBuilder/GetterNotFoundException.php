<?php

declare(strict_types=1);

namespace App\Service\JsonApi\ResponseBuilder;

class GetterNotFoundException extends \Exception
{
    public function __construct(string $getterName)
    {
        $message = \sprintf("Getter '%s' does not exist.", $getterName);
        parent::__construct($message);
    }
}