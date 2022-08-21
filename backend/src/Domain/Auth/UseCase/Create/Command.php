<?php

declare(strict_types=1);

namespace App\Domain\Auth\UseCase\Create;

class Command
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    public string $firstName;
    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    public string $lastName;
    /**
     * @Assert\NotBlank
     * @Assert\Email
     */
    public string $email;
}