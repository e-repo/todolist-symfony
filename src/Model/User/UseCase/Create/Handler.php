<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Create;


use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\PasswordHasher;
use App\ReadModel\User\UserFetcher;
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