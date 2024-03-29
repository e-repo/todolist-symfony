<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Image\Attach;

use App\Domain\Auth\User\Entity\Image\Image;
use App\Domain\Auth\User\Entity\User\Id;
use App\Domain\Auth\User\Repository\UserRepository;
use App\Infrastructure\Upload\UploadHelper;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private UploadHelper $uploadHelper;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    /**
     * Handler constructor.
     * @param UploadHelper $uploadHelper
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        UploadHelper $uploadHelper,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->uploadHelper = $uploadHelper;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Command $command
     * @throws \League\Flysystem\FileExistsException
     */
    public function handle(Command $command): void
    {
        $user = $this->userRepository->getById(new Id($command->userId));

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $fileName = $this->uploadHelper->getNewFileName($command->file);
            $image = new Image($fileName, $command->file, $user);

            $user->attachImage($image);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->uploadHelper->uploadFile(
                $command->file,
                (new \ReflectionClass($user))->getShortName(),
                $user->getId()->getValue(),
                $fileName
            );

            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw new \RuntimeException($e->getMessage());
        }
    }
}