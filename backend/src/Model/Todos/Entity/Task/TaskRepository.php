<?php

declare(strict_types=1);

namespace App\Model\Todos\Entity\Task;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ObjectRepository;

class TaskRepository
{
    private ObjectRepository $repository;
    private EntityManagerInterface $entityManager;

    /**
     * TaskRepository constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Task::class);
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param Id $id
     * @return Task
     * @throws EntityNotFoundException
     */
    public function get(Id $id): Task
    {
        if (! $task = $this->repository->find($id)) {
            throw new EntityNotFoundException('Task is not found.');
        }

        return $task;
    }
}