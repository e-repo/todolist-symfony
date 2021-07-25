<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

class UserFetcher extends ServiceEntityRepository
{
    /**
     * UserFetcher constructor.
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry, Connection $connection)
    {
        $connection->createQueryBuilder();
        parent::__construct($managerRegistry, User::class);
    }

    /**
     * Проверка существования пользователя по email
     *
     * @param string $email
     * @return bool
     */
    public function hasByEmail(string $email): bool
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('t.email = :email')
            ->setParameter(':email', $email)
            ->getQuery()
            ->execute() > 0;
    }

    /**
     * Проверка существования пользователя по соцсети
     *
     * @param string $network
     * @param string $identity
     * @return bool
     */
    public function hasByNetworkIdentity(string $network, string $identity): bool
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->leftJoin('u.networks', 'n')
            ->where('n.network = :network and n.identity = :identity')
            ->setParameter(':network', $network)
            ->setParameter(':identity', $identity)
            ->getQuery()
            ->execute() > 0;
    }

    /**
     * Проверка существования соцсети у пользователя
     *
     * @param Id $id
     * @param string $network
     * @param string $identity
     * @return bool
     */
    public function hasNetwork(Id $id, string $network, string $identity): bool
    {
        return $this->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->innerJoin('u.networks', 'n')
                ->where('u.id = :uuid')
                ->andWhere('n.network = :network and n.identity = :identity')
                ->setParameter(':uuid', $id->getValue())
                ->setParameter(':network', $network)
                ->setParameter(':identity', $identity)
                ->getQuery()
                ->execute() > 0;
    }

    /**
     * Возвращает пользователя по email для аутентификации
     *
     * @param string $email
     * @return AuthView|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findForAuthByEmail(string $email): ?AuthView
    {
        $result = $this->createQueryBuilder('u')
            ->select('u.id', 'u.passwordHash', 'u.email', 'u.role', 'u.status')
            ->where('u.email = :email')
            ->setParameter(':email', $email)
            ->getQuery()
            ->getOneOrNullResult();

        return $result ? AuthView::fromArray($result) : null;
    }

    /**
     * Возвращает AuthView - пользователя по соцсети
     *
     * @param string $network
     * @param string $identity
     * @return AuthView|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findForAuthByNetwork(string $network, string $identity): ?AuthView
    {
        $result = $this->createQueryBuilder('u')
            ->select('u.id', 'u.email', 'u.passwordHash', 'u.role', 'u.status')
            ->innerJoin('u.networks', 'n')
            ->where('n.network = :network and n.identity = :identity')
            ->setParameter(':network', $network)
            ->setParameter(':identity', $identity)
            ->getQuery()
            ->getOneOrNullResult();

        return $result ? AuthView::fromArray($result) : null;
    }

    /**
     * Проверка наличия токена для сброса пароля
     *
     * @param string $token
     * @return bool
     */
    public function existByResetToken(string $token): bool
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('t.reset_token = :token')
            ->setParameter(':token', $token)
            ->getQuery()
            ->execute() > 0;
    }
}