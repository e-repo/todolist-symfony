<?php

declare(strict_types=1);

namespace App\Domain\Auth\UseCase\Name;

use App\Domain\Auth\Entity\User\Id;
use App\Domain\Auth\Entity\User\Name;
use App\Domain\Auth\Entity\User\UserRepository;
use App\Domain\Service\Flusher;

class Handler
{
    private Flusher $flusher;
    private UserRepository $users;

    /**
     * Handler constructor.
     * @param UserRepository $users
     * @param Flusher $flusher
     */
    public function __construct(UserRepository $users,Flusher $flusher)
    {
        $this->flusher = $flusher;
        $this->users = $users;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));
        $user->changeName(new Name($command->first, $command->last));
        $this->flusher->flush();
    }
}