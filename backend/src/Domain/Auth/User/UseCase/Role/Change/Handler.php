<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Role\Change;

use App\Domain\Auth\User\Entity\User\Id;
use App\Domain\Auth\User\Entity\User\Role;
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
     * @param EntityManagerInterface $em
     * @param Flusher $flusher
     * @param UserRepository $users
     */
    public function __construct(
        EntityManagerInterface $em,
        Flusher $flusher,
        UserRepository $users
    )
    {
        $this->users = $users;
        $this->em = $em;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $user = $this->users->get(new Id($command->uuid));

        $user->changeRole(new Role($command->role));

        $this->em->persist($user);
        $this->flusher->flush();
    }
}