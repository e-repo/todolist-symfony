<?php

declare(strict_types=1);

namespace App\Model\Todos\UseCase\Create;

use App\Model\Flusher;
use App\Model\Todos\Entity\Task\Content;
use App\Model\Todos\Entity\Task\Id;
use \App\Model\User\Entity\User\Id as UserId;
use App\Model\Todos\Entity\Task\Task;
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
     * @param EntityManagerInterface $em
     * @param Flusher $flusher
     */
    public function __construct(UserRepository $users, EntityManagerInterface $em, Flusher $flusher)
    {
        $this->users = $users;
        $this->em = $em;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new UserId($command->userId));
        $content = new Content($command->name, $command->desctiption);

        $task = new Task(
            Id::next(),
            $user,
            $content,
            new \DateTimeImmutable()
        );

        $this->em->persist($task);
        $this->flusher->flush();
    }
}