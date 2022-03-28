<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase\Edit;

use App\Domain\User\Entity\User\User;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank
     */
    public string $uuid;
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

    public static function createFromUser(User $user): self
    {
        $command = new self();

        $command->uuid = $user->getId()->getValue() ?? '';
        $command->email = $user->getEmail()->getValue() ?? '';
        $command->firstName = $user->getName()->getFirst() ?? '';
        $command->lastName = $user->getName()->getLast() ?? '';

        return $command;
    }
}