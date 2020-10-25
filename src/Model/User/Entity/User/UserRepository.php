<?php


namespace App\Model\User\Entity\User;


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
    public function findByResetToken(string $token)
    {
        return $this->repo->findOneBy(['resetToken.token' => $token]);
    }

    /**
     * Возвращаент пользователя по токену регистрации
     *
     * @param string $token
     * @return User|object|null
     */
    public function findByConfirmToken(string $token)
    {
        return $this->repo->findOneBy(['confirmToken' => $token]);
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }
}