<?php

declare(strict_types=1);

namespace App\Domain\Auth\UseCase\Email\Request;

use App\Domain\Auth\Entity\User\Email;
use App\Domain\Auth\Entity\User\Id;
use App\Domain\Auth\Entity\User\UserRepository;
use App\Domain\Auth\Read\UserFetcher;
use App\Domain\Auth\Service\NewEmailConfirmTokenizer;
use App\Domain\Auth\Service\NewEmailConfirmTokenSender;
use App\Domain\Service\Flusher;

class Handler
{
    /**
     * @var UserRepository
     */
    private UserRepository $users;
    /**
     * @var NewEmailConfirmTokenizer
     */
    private NewEmailConfirmTokenizer $tokenizer;
    /**
     * @var NewEmailConfirmTokenSender
     */
    private NewEmailConfirmTokenSender $sender;
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
     * @param NewEmailConfirmTokenizer $tokenizer
     * @param NewEmailConfirmTokenSender $sender
     * @param Flusher $flusher
     */
    public function __construct(
        UserRepository $users,
        UserFetcher $userFetcher,
        NewEmailConfirmTokenizer $tokenizer,
        NewEmailConfirmTokenSender $sender,
        Flusher $flusher
    )
    {
        $this->users = $users;
        $this->userFetcher = $userFetcher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $email = new Email($command->email);

        if ($this->userFetcher->hasByEmail($command->email)) {
            throw new \DomainException('Email is already in use.');
        }

        $user->requestEmailChanging(
            $email,
            $token = $this->tokenizer->generate()
        );

        $this->flusher->flush();
        $this->sender->send($email, $token);
    }
}