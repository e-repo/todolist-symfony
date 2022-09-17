<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Reset\Reset;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     * @var string
     */
    public string $password;
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $token;

    public function __construct($token)
    {
        $this->token = $token;
    }
}