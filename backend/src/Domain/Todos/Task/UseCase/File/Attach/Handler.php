<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\UseCase\File\Attach;

use App\Domain\Todos\Task\Entity\File;
use App\Domain\Todos\Task\Entity\Task\Id;
use App\Domain\Todos\Task\Repository\TaskRepository;
use App\Infrastructure\Upload\UploadHelper;
use Doctrine\ORM\EntityManagerInterface;


class Handler
{
    private UploadHelper $uploadHelper;
    private TaskRepository $taskRepository;
    private EntityManagerInterface $entityManager;

    /**
     * Handler constructor.
     * @param UploadHelper $uploadHelper
     * @param TaskRepository $taskRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        UploadHelper $uploadHelper,
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->uploadHelper = $uploadHelper;
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Command $command
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function handle(Command $command): void
    {
        $task = $this->taskRepository->get(new Id($command->taskId));

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $fileName = $this->uploadHelper->getNewFileName($command->file);
            $file = new File($fileName, $command->file, $task);

            $task->attachFile($file);

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->uploadHelper->uploadFile(
                $command->file,
                (new \ReflectionClass($task))->getShortName(),
                $task->getId()->getValue(),
                $fileName
            );

            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw new \RuntimeException($e->getMessage());
        }
    }
}