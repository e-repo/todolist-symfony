<?php

declare(strict_types=1);

namespace App\Domain\Auth\UseCase\SignUp\Confirm\ByToken;

class Command
{
    public string $token;

    /**
     * Command constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }
}