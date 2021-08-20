<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\ReadModel\User\Filter\Filter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\UnexpectedResultException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class UserFetcher extends ServiceEntityRepository
{
    private Connection $connection;
    private PaginatorInterface $paginator;

    /**
     * UserFetcher constructor.
     * @param ManagerRegistry $managerRegistry
     * @param Connection $connection
     * @param PaginatorInterface $paginator
     */
    public function __construct(ManagerRegistry $managerRegistry, Connection $connection, PaginatorInterface $paginator)
    {
        parent::__construct($managerRegistry, User::class);
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    /**
     * Проверка существования пользователя по email
     *
     * @param string $email
     * @return bool
     */
    public function hasByEmail(string $email): bool
    {
        try {
            $result = $this->createQueryBuilder('u')
                    ->select('COUNT(u.id)')
                    ->andWhere('u.email = :email')
                    ->setParameter(':email', $email)
                    ->getQuery()
                    ->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return false;
        }

        return $result > 0;
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
        try {
            $result = $this->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->leftJoin('u.networks', 'n')
                ->where('n.network = :network and n.identity = :identity')
                ->setParameter(':network', $network)
                ->setParameter(':identity', $identity)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return false;
        }

        return $result > 0;
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
        try {
            $result = $this->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->innerJoin('u.networks', 'n')
                ->where('u.id = :uuid')
                ->andWhere('n.network = :network and n.identity = :identity')
                ->setParameter(':uuid', $id->getValue())
                ->setParameter(':network', $network)
                ->setParameter(':identity', $identity)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return false;
        }

        return $result > 0;
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
            ->select(
                'u.id',
                'u.name.first as firstName',
                'u.name.last as lastName',
                'u.passwordHash', 'u.email',
                'u.role',
                'u.status'
            )
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
            ->select(
                'u.id',
                'u.name.first as firstName',
                'u.name.last as lastName',
                'u.email',
                'u.passwordHash',
                'u.role',
                'u.status'
            )
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
        try {
            $result = $this->createQueryBuilder('u')
                    ->select('COUNT(u.id)')
                    ->where('u.resetToken.token = :token')
                    ->setParameter(':token', $token)
                    ->getQuery()
                    ->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return false;
        }

        return $result > 0;
    }

    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'date',
                'TRIM(CONCAT(name_first, \' \', name_last)) AS name',
                'email',
                'role',
                'status'
            )
            ->from('user_users');

        if ($filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(CONCAT(name_first, \' \', name_last))', ':name'));
            $qb->setParameter(':name', $filter->name);
        }

        if ($filter->email) {
            $qb->andWhere($qb->expr()->like('LOWER(email)', ':email'));
            $qb->setParameter(':email', $filter->email);
        }

        if ($filter->role) {
            $qb->andWhere('role = :role');
            $qb->setParameter(':role', $filter->role);
        }

        if ($filter->status) {
            $qb->andWhere('status = :status');
            $qb->setParameter(':status', $filter->status);
        }

        if (! \in_array($sort, ['date', 'name', 'email', 'role', 'status'], true)) {
            throw new \UnexpectedValueException(sprintf('Cannot sort by %s', $sort));
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}