<?php


namespace App\Domain\Auth\User\Repository;

use App\Domain\Auth\User\Entity\Image\Image;
use App\Domain\Auth\User\Entity\User\Id;
use App\Domain\Auth\User\Entity\User\User;
use App\Domain\Service\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Image>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Возвращает пользователя по токену на сброс пароля
     *
     * @param string $token
     * @return User|object|null
     */
    public function findByResetToken(string $token): ?User
    {
        return $this->findOneBy(['resetToken.token' => $token]);
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
        return $this->find(new Id($uuid));
    }

    /**
     * @param string $email
     * @return User|object
     */
    public function getByEmail(string $email): User
    {
        if (! $user = $this->findOneBy(['email' => $email])) {
            throw new EntityNotFoundException('User is not found.');
        }
        return $user;
    }

    /**
     * @param Id $id
     * @return User|object
     * @throws EntityNotFoundException
     */
    public function getById(Id $id): User
    {
        if (! $user = $this->find($id)) {
            throw new EntityNotFoundException('User is not found.');
        }
        return $user;
    }

    /**
     * @param string $uuid
     * @return User
     * @throws EntityNotFoundException
     */
    public function getByUuid(string $uuid): User
    {
        return $this->getById(new Id($uuid));
    }
}
