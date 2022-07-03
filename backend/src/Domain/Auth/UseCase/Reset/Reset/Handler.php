<?php

namespace App\Domain\Auth\UseCase\Reset\Reset;

use App\Domain\Auth\Entity\User\UserRepository;
use App\Domain\Auth\Service\PasswordHasher;
use App\Domain\Service\Flusher;

class Handler
{
    /**
     * @var UserRepository
     */
    private UserRepository $users;
    /**
     * @var PasswordHasher
     */
    private PasswordHasher $hasher;
    /**
     * @var Flusher
     */
    private Flusher $flusher;

    /**
     * Handler constructor.
     * @param UserRepository $users
     * @param PasswordHasher $hasher
     * @param Flusher $flusher
     */
    public function __construct(UserRepository $users, PasswordHasher $hasher, Flusher $flusher)
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        if (! $user = $this->users->findByResetToken($command->token)) {
            throw new \DomainException('Incorrect or confirmed token.');
        }

        $user->passwordReset(new \DateTimeImmutable(), $this->hasher->hash($command->password));

        $this->flusher->flush();
    }
}