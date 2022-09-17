<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\SignUp\Confirm\Manual;

class Command
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}