<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Auth;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\ReadModel\User\UserFetcher;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;
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
     * @param EntityManagerInterface $em
     * @param UserFetcher $userFetcher
     * @param Flusher $flusher
     */
    public function __construct(EntityManagerInterface $em, UserFetcher $userFetcher, Flusher $flusher)
    {
        $this->em = $em;
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
            new Name($command->firstName, $command->lastName),
            $command->network,
            $command->identity
        );

        $this->em->persist($user);
        $this->flusher->flush();
    }
}