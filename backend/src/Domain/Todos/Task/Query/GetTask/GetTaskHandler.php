<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\Query\GetTask;

use App\Domain\Todos\Task\Dto\TaskPresenterDto;
use App\Domain\Todos\Task\Entity\Task\Id;
use App\Domain\Todos\Task\Repository\TaskRepository;

class GetTaskHandler
{
    private TaskRepository $taskRepository;

    public function __construct(
        TaskRepository $taskRepository
    )
    {
        $this->taskRepository = $taskRepository;
    }

    /**\
     * @param GetTaskQuery $query
     * @return TaskPresenterDto
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function handle(GetTaskQuery $query): TaskPresenterDto
    {
        $task = $this->taskRepository->get(new Id($query->getTaskUuid()));

        return (new TaskPresenterDto())
            ->setUuid($task->getId()->getValue())
            ->setUserUuid($task->getUserUuid())
            ->setName($task->getName())
            ->setDescription($task->getDescription())
            ->setDate($task->getDate())
            ->setStatus($task->getStatus());
    }
}
