<?php


namespace App\Domain\Auth\User\Repository;

use App\Domain\Auth\User\Entity\User\Id;
use App\Domain\Auth\User\Entity\User\User;
use App\Domain\Service\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class UserRepository
{
    private ObjectRepository $repository;

    /**
     * UserRepository constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository(User::class);
    }

    /**
     * Возвращает пользователя по токену на сброс пароля
     *
     * @param string $token
     * @return User|object|null
     */
    public function findByResetToken(string $token): ?User
    {
        return $this->repository->findOneBy(['resetToken.token' => $token]);
    }

    /**
     * Возвращает пользователя по токену регистрации
     *
     * @param string $token
     * @return User|object|null
     */
    public function findByConfirmToken(string $token): ?User
    {
        return $this->repository->findOneBy(['confirmToken' => $token]);
    }

    /**
     * @param string $uuid
     * @return User|null
     */
    public function findById(string $uuid): ?User
    {
        return $this->repository->find(new Id($uuid));
    }

    /**
     * @param string $email
     * @return User|object
     */
    public function getByEmail(string $email): User
    {
        if (! $user = $this->repository->findOneBy(['email' => $email])) {
            throw new EntityNotFoundException('User is not found.');
        }
        return $user;
    }

    /**
     * @param Id $id
     * @return User|object
     */
    public function getById(Id $id): User
    {
        if (! $user = $this->repository->find($id)) {
            throw new EntityNotFoundException('User is not found.');
        }
        return $user;
    }

    public function getByUuid(string $uuid): User
    {
        return $this->getById(new Id($uuid));
    }
}