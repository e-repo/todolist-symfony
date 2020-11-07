<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Auth;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\ReadModel\User\UserFetcher;

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
     * @var UserFetcher
     */
    private UserFetcher $userFetcher;

    /**
     * Handler constructor.
     * @param UserRepository $users
     * @param UserFetcher $userFetcher
     * @param Flusher $flusher
     */
    public function __construct(UserRepository $users, UserFetcher $userFetcher, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->userFetcher = $userFetcher;
    }

    public function handle(Command $command)
    {
        if ($this->userFetcher->hasByNetworkIdentity($command->network, $command->identity)) {
            throw new \DomainException('User already exist');
        }

        $user = User::signUpByNetwork(
            Id::next(),
            new \DateTimeImmutable(),
            $command->network,
            $command->identity
        );

        $this->users->add($user);
        $this->flusher->flush();
    }
}