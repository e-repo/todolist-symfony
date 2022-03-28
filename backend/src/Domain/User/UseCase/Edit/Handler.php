<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase\Edit;


use App\Domain\Service\Flusher;
use App\Domain\User\Entity\User\Email;
use App\Domain\User\Entity\User\Id;
use App\Domain\User\Entity\User\Name;
use App\Domain\User\Entity\User\UserRepository;
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

        $user->edit(
            new Email($command->email),
            new Name($command->firstName, $command->lastName)
        );

        $this->em->persist($user);
        $this->flusher->flush();
    }
}