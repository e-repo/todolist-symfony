<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\UseCase\Delete;

use App\Domain\Todos\Task\Entity\Task\Id;
use App\Domain\Todos\Task\Repository\TaskRepository;
use Doctrine\ORM\EntityNotFoundException;

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
     * @return void
     * @throws EntityNotFoundException
     */
    public function handle(Command $command): void
    {
        $task = $this->taskRepository->get(new Id($command->taskUuid));

        $task->delete();
        $entityManager = $this->taskRepository->getEntityManager();
        $entityManager->persist($task);
        $entityManager->flush();
    }
}
