<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\UseCase\Create;

use App\Domain\Service\Flusher;
use App\Domain\Todos\AuthAdapter\AuthAdapter;
use App\Domain\Todos\Task\Entity\Task\Content;
use App\Domain\Todos\Task\Entity\Task\Id;
use App\Domain\Todos\Task\Entity\Task\Task;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private EntityManagerInterface $em;
    private Flusher $flusher;
    private AuthAdapter $authAdapter;

    /**
     * Handler constructor.
     * @param AuthAdapter $authAdapter
     * @param EntityManagerInterface $em
     * @param Flusher $flusher
     */
    public function __construct(
        AuthAdapter $authAdapter,
        EntityManagerInterface $em,
        Flusher $flusher
    )
    {
        $this->em = $em;
        $this->flusher = $flusher;
        $this->authAdapter = $authAdapter;
    }

    public function handle(Command $command): void
    {
        $user = $this->authAdapter->getUserByUuid($command->userId);
        $content = new Content($command->name, $command->description);

        $task = new Task(
            Id::next(),
            $user->getId(),
            $content,
            new \DateTimeImmutable()
        );

        $this->em->persist($task);
        $this->flusher->flush();
    }
}
