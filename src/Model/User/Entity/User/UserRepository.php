<?php


namespace App\Model\User\Entity\User;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
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

    public function findByResetToken(string $token): ?User
    {
        return $this->repo->findOneBy(['resetToken.token' => $token]);
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }
}