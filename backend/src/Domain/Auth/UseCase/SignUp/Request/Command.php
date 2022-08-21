<?php

declare(strict_types=1);

namespace App\Domain\Auth\UseCase\SignUp\Request;

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
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    public string $password;
}