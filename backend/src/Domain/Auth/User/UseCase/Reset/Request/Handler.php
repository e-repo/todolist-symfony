<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Reset\Request;

use App\Domain\Auth\User\Repository\UserRepository;
use App\Domain\Auth\User\Service\ResetTokenizer;
use App\Domain\Auth\User\Service\ResetTokenSender;
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
     * @var ResetTokenizer
     */
    private ResetTokenizer $tokenizer;
    /**
     * @var ResetTokenSender
     */
    private ResetTokenSender $sender;

    /**
     * Handler constructor.
     * @param UserRepository $users
     * @param Flusher $flusher
     * @param ResetTokenizer $tokenizer
     * @param ResetTokenSender $sender
     */
    public function __construct(
        UserRepository $users,
        Flusher $flusher,
        ResetTokenizer $tokenizer,
        ResetTokenSender $sender
    )
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
    }

    public function handle(Command $command)
    {
        $user = $this->users->getByEmail($command->email);

        $user->requestPasswordReset(
            $this->tokenizer->generate(),
            new \DateTimeImmutable()
        );

        $this->sender->send($user->getEmail(), $user->getRequestToken());
        $this->flusher->flush();
    }
}