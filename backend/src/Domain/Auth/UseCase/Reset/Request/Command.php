<?php

declare(strict_types=1);

namespace App\Domain\Auth\UseCase\Reset\Request;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @var string
     */
    public string $email;
}