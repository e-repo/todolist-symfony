<?php

declare(strict_types=1);

namespace App\Model\Todos\UseCase\Published;

use App\Model\Todos\Entity\Task\Id;
use App\Model\Todos\Entity\Task\TaskRepository;

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

    public function handle(Command $command): void
    {
        $task = $this->taskRepository->get(new Id($command->taskId));

        $task->published();
        $entityManager = $this->taskRepository->getEntityManager();
        $entityManager->persist($task);
        $entityManager->flush();
    }
}