<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Role\Change;

use App\Model\User\Entity\User\User;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank
     */
    public string $uuid;
    /**
     * @Assert\NotBlank
     */
    public string $role;

    public static function createFromUser(User $user): self
    {
        $command = new self();

        $command->uuid = $user->getId()->getValue() ?? '';
        $command->role = $user->getRole()->name() ?? '';

        return $command;
    }
}