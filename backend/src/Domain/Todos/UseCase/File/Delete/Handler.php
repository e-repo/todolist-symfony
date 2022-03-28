<?php

declare(strict_types=1);

namespace App\Domain\Todos\UseCase\File\Delete;


use App\Domain\Todos\Entity\Task\FileRepository;
use App\Infrastructure\Upload\UploadHelper;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private UploadHelper $uploadHelper;
    private FileRepository $fileRepository;
    private EntityManagerInterface $entityManager;

    /**
     * Handler constructor.
     * @param UploadHelper $uploadHelper
     * @param FileRepository $fileRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        UploadHelper $uploadHelper,
        FileRepository $fileRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->uploadHelper = $uploadHelper;
        $this->fileRepository = $fileRepository;
        $this->entityManager = $entityManager;
    }

    public function handle(Command $command): void
    {
        $file = $this->fileRepository->get($command->fileId);

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $this->entityManager->remove($file);
            $this->entityManager->flush();

            $this->uploadHelper->deleteFile($file->getFilePath());

            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw new \RuntimeException($e->getMessage());
        }
    }
}