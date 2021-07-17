<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Attach;

use App\Model\User\Entity\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\User\Entity\User\Id;
use App\Model\Flusher;

class Handler
{
    private UserRepository $users;
    private Flusher $flusher;
    private EntityManagerInterface $em;

    /**
     * Handler constructor.
     * @param EntityManagerInterface $em
     * @param UserRepository $users
     * @param Flusher $flusher
     */
    public function __construct(EntityManagerInterface $em, UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->em = $em;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command)
    {
        $user = $this->users->get(new Id($command->uuid));
        $user->attachNetwork($command->network, $command->networkIdentity);

        $this->em->persist($user);
        $this->flusher->flush();
    }
}