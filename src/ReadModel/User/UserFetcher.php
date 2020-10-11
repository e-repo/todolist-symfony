<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\User\Entity\User\User;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;

class UserFetcher
{
    /**
     * @var Connection
     */
    private Connection $connection;
    private QueryBuilder $qb;
    private string $entityClassName;


    /**
     * UserFetcher constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->qb = $connection->createQueryBuilder();
        $this->entityClassName = User::class;
    }

    /**
     * @param string $email
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasByEmail(string $email)
    {
        return $this->qb
                ->select('COUNT(t.id)')
                ->from($this->entityClassName, 't')
                ->andWhere('t.email = :email')
                ->setParameter(':email', $email)
                ->getQuery()->getSingleScalarResult() > 0;
    }
}