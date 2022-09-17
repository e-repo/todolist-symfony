<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Repository;

use App\Domain\Auth\User\Entity\Image\Image;
use App\Domain\Auth\User\Entity\User\Id;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ImageRepository
{
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository(Image::class);
    }

    public function findActiveImageByUserId(Id $id): ?Image
    {
        return $this->repository->findOneBy(['user' => $id, 'isActive' => true]);
    }
}