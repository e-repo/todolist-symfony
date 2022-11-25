<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Image\Activate;

use App\Domain\Auth\User\Dto\Image;
use App\Domain\Auth\User\Entity\User\Id;
use App\Domain\Auth\User\Error\Image\ImageNotBelongsToUserException;
use App\Domain\Auth\User\Error\Image\UserImageNotFoundException;
use App\Domain\Auth\User\Repository\ImageRepository;
use App\Domain\Service\Flusher;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;

class Handler
{
    private ImageRepository $imageRepository;
    private Flusher $flusher;
    private Connection $connection;

    public function __construct(
        ImageRepository $imageRepository,
        Flusher $flusher,
        Connection $connection
    )
    {
        $this->imageRepository = $imageRepository;
        $this->flusher = $flusher;
        $this->connection = $connection;
    }

    /**
     * @throws UserImageNotFoundException
     * @throws ConnectionException
     * @throws \Throwable
     */
    public function handle(Command $command): Image
    {
        $image = $this->imageRepository->find($command->imageUuid);

        if (null === $image) {
            throw new UserImageNotFoundException(['imageUuid' => $command->imageUuid]);
        }

        if (false === $image->isImageBelongsToUser($command->userUuid)) {
            throw new ImageNotBelongsToUserException($command->userUuid);
        }

        $this->connection->beginTransaction();

        try {
            $image->setActive();
            $this->flusher->persist($image);

            $activeUserImage = $this->imageRepository->findActiveImageByUserId(new Id($command->userUuid));
            $activeUserImage->setInactive();
            $this->flusher->persist($activeUserImage);

            $this->flusher->flush();
            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollBack();

            throw $e;
        }


        return new Image(
            $image->getId(),
            $image->getFilename(),
            $image->getOriginalFilename(),
            $image->getFilePath(),
            $image->isActive()
        );
    }
}
