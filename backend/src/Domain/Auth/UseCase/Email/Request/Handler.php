<?php

declare(strict_types=1);

namespace App\Domain\Auth\UseCase\Email\Request;

use App\Domain\Auth\Entity\User\Email;
use App\Domain\Auth\Entity\User\Id;
use App\Domain\Auth\Entity\User\UserRepository;
use App\Domain\Auth\Read\UserFetcher;
use App\Domain\Auth\Service\ConfirmEmailSender;
use App\Domain\Service\Flusher;

class Handler
{
    /**
     * @var UserRepository
     */
    private UserRepository $users;
    /**
     * @var ConfirmEmailSender
     */
    private ConfirmEmailSender $sender;
    /**
     * @var Flusher
     */
    private Flusher $flusher;
    /**
     * @var UserFetcher
     */
    private UserFetcher $userFetcher;

    /**
     * Handler constructor.
     * @param UserRepository $users
     * @param UserFetcher $userFetcher
     * @param ConfirmEmailSender $sender
     * @param Flusher $flusher
     */
    public function __construct(
        UserRepository           $users,
        UserFetcher              $userFetcher,
        ConfirmEmailSender       $sender,
        Flusher                  $flusher
    )
    {
        $this->users = $users;
        $this->userFetcher = $userFetcher;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $email = new Email($command->newEmail);

        if ($this->userFetcher->hasByEmail($command->newEmail)) {
            throw new \DomainException('Email is already in use.');
        }

        $user->requestEmailChanging(
            $email,
            $command->token
        );

        $this->flusher->flush();
        $this->sender->send($email, $command->confirmUrl);
    }
}