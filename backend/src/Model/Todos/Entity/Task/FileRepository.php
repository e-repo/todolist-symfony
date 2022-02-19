<?php

declare(strict_types=1);

namespace App\Model\Todos\Entity\Task;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ObjectRepository;

class FileRepository
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
        $this->repository = $entityManager->getRepository(File::class);
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param string $id
     * @return File
     * @throws EntityNotFoundException
     */
    public function get(string $id): File
    {
        if (! $file = $this->repository->find($id)) {
            throw new EntityNotFoundException('File is not found.');
        }

        return $file;
    }

    /**
     * @param File $file
     * @return bool
     */
    public function delete(File $file): bool
    {
        return $this->repository->delete($file);
    }
}