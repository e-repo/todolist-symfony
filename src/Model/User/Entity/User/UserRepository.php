<?php


namespace App\Model\User\Entity\User;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class UserRepository
{
    private ObjectRepository $repo;
    private EntityManagerInterface $em;

    /**
     * UserRepository constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $this->em->getRepository(User::class);
    }

    /**
     * Возвращает пользователя по токену на сброс пароля
     *
     * @param string $token
     * @return User|object|null
     */
    public function findByResetToken(string $token): ?User
    {
        return $this->repo->findOneBy(['resetToken.token' => $token]);
    }

    /**
     * Возвращаент пользователя по токену регистрации
     *
     * @param string $token
     * @return User|object|null
     */
    public function findByConfirmToken(string $token): ?User
    {
        return $this->repo->findOneBy(['confirmToken' => $token]);
    }

    /**
     * @param string $email
     * @return User|object
     */
    public function getByEmail(string $email): ?User
    {
        if (! $user = $this->repo->findOneBy(['email' => $email])) {
            throw new EntityNotFoundException('User is not found.');
        }
        return $user;
    }

    /**
     * @param User $user
     */
    public function add(User $user): void
    {
        $this->em->persist($user);
    }
}