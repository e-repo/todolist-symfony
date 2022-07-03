<?php

declare(strict_types=1);

namespace App\Domain\Auth\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class NetworkRepository
{
    private ObjectRepository $repository;

    /**
     * NetworkRepository constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository(Network::class);
    }

    public function findByNetworkIdentity(string $network, string $identity): ?Network
    {
        return $this->repository->findOneBy(['network' => $network, 'identity' => $identity]);
    }

    /**
     * @param Id $id
     * @return array
     */
    public function findByUserId(Id $id): array
    {
        return $this->repository->findBy(['user' => $id]);
    }
}