<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Email\ChangeRequest;

use App\Domain\Auth\User\Entity\User\Email;
use App\Domain\Auth\User\Entity\User\Id;
use App\Domain\Auth\User\Read\UserFetcher;
use App\Domain\Auth\User\Repository\UserRepository;
use App\Domain\Auth\User\Service\ConfirmEmailSender;
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
        $user = $this->users->getById(new Id($command->id));

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