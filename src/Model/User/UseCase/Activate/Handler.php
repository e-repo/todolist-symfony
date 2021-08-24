<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Activate;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
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