<?php

declare(strict_types=1);

namespace App\Domain\Auth\UseCase\Email\confirm;

use App\Domain\Auth\Entity\User\Id;
use App\Domain\Auth\Entity\User\UserRepository;
use App\Domain\Service\Flusher;

class Handler
{
    /**
     * @var UserRepository
     */
    private UserRepository $users;
    /**
     * @var Flusher
     */
    private Flusher $flusher;

    /**
     * Handler constructor.
     * @param UserRepository $users
     * @param Flusher $flusher
     */
    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));
        $user->confirmEmailChanging($command->token);
        $this->flusher->flush();
    }
}