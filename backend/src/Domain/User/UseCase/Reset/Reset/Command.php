<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase\Reset\Reset;

use Symfony\Component\Validator\Constraints as Assert;

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