<?php

declare(strict_types=1);

namespace App\Domain\Auth\UseCase\SignUp\Confirm\ByToken;

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

    public function handle(Command $command)
    {
        if (! $user = $this->users->findByConfirmToken($command->token)) {
            throw new \DomainException('Incorrect on confirmed token.');
        }

        $user->confirmSignUp();
        $this->flusher->flush();
    }
}