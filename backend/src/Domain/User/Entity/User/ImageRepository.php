<?php

declare(strict_types=1);

namespace App\Domain\User\Entity\User;

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