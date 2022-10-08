<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\UseCase\Create;

use App\Domain\Auth\User\Repository\UserRepository;
use App\Domain\Service\Flusher;
use App\Domain\Todos\Task\Entity\Task\Content;
use App\Domain\Todos\Task\Entity\Task\Id;
use App\Domain\Todos\Task\Entity\Task\Task;
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
        $user = $this->users->getByUuid($command->userId);
        $content = new Content($command->name, $command->description);

        $task = new Task(
            Id::next(),
            $user->getId()->getValue(),
            $content,
            new \DateTimeImmutable()
        );

        $this->em->persist($task);
        $this->flusher->flush();
    }
}