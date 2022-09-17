<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Activate;

use App\Domain\Auth\User\Entity\User\Id;
use App\Domain\Auth\User\Repository\UserRepository;
use App\Domain\Service\Flusher;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private UserRepository $users;
    private EntityManagerInterface $em;
    private Flusher $flusher;

    /**
     * Handler constructor.
     * @param UserRepository $users
     * @param Flusher $flusher
     * @param EntityManagerInterface $em
     */
    public function __construct(UserRepository $users, Flusher $flusher, EntityManagerInterface $em)
    {
        $this->users = $users;
        $this->em = $em;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->uuid));
        $user->activate();

        $this->em->persist($user);
        $this->flusher->flush();
    }
}