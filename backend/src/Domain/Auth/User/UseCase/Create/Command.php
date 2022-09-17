<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

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