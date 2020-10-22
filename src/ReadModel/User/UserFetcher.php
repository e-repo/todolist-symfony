<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\User\Entity\User\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Query\QueryBuilder;

class UserFetcher
{
    /**
     * @var Connection
     */
    private Connection $connection;
    private QueryBuilder $qb;

    /**
     * Наименование таблицы бд для
     * read-model
     */
    public const TABLE_NAME = 'user_users';


    /**
     * UserFetcher constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->qb = $connection->createQueryBuilder();
    }

    /**
     * @param string $email
     * @return bool
     */
    public function hasByEmail(string $email)
    {
        return $this->qb
            ->select('COUNT(t.id)')
            ->from(self::TABLE_NAME, 't')
            ->andWhere('t.email = :email')
            ->setParameter(':email', $email)
            ->execute()->fetchColumn(0) > 0;
    }

    /**
     * @param string $email
     * @return AuthView|null
     */
    public function findForAuth(string $email): ?AuthView
    {
        $stmt = $this->qb
            ->select('id', 'email', 'password_hash', 'role', 'status')
            ->from(self::TABLE_NAME)
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }
}