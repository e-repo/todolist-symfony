<?php

declare(strict_types=1);

namespace App\Domain\Todos\UseCase\Update;

use App\Domain\Todos\Entity\Task\Content;
use App\Domain\Todos\Entity\Task\Id;
use App\Domain\Todos\Entity\Task\TaskRepository;

class Handler
{
    private TaskRepository $taskRepository;

    /**
     * Handler constructor.
     * @param TaskRepository $taskRepository
     */
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * @param Command $command
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function handle(Command $command): void
    {
        $task = $this->taskRepository->get(new Id($command->taskId));

        $content = new Content($command->name, $command->description);
        $task->changeContent($content);

        $em = $this->taskRepository->getEntityManager();
        $em->persist($task);
        $em->flush();
    }
}