<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Create;

use App\Domain\Auth\User\Entity\User\Email;
use App\Domain\Auth\User\Entity\User\Id;
use App\Domain\Auth\User\Entity\User\Name;
use App\Domain\Auth\User\Entity\User\User;
use App\Domain\Auth\User\Read\UserFetcher;
use App\Domain\Auth\User\Service\PasswordHasher;
use App\Domain\Service\Flusher;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private UserFetcher $fetcher;
    private PasswordHasher $hasher;
    private EntityManagerInterface $em;
    private Flusher $flusher;

    /**
     * Handler constructor.
     * @param EntityManagerInterface $em
     * @param Flusher $flusher
     * @param UserFetcher $fetcher
     * @param PasswordHasher $hasher
     */
    public function __construct(
        EntityManagerInterface $em,
        Flusher $flusher,
        UserFetcher $fetcher,
        PasswordHasher $hasher
    )
    {
        $this->fetcher = $fetcher;
        $this->hasher = $hasher;
        $this->em = $em;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        if ($user = $this->fetcher->hasByEmail($command->email)) {
            throw new \DomainException('User already exist.');
        }

        $user = User::signUpByManual(
            Id::next(),
            new \DateTimeImmutable(),
            new Name($command->firstName, $command->lastName),
            new Email($command->email),
            $this->hasher->hashRandom()
        );

        $this->em->persist($user);
        $this->flusher->flush();
    }
}
