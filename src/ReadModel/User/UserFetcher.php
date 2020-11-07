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
    }

    /**
     * Проверка существования пользователя по email
     *
     * @param string $email
     * @return bool
     */
    public function hasByEmail(string $email): bool
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT(t.id)')
            ->from(self::TABLE_NAME, 't')
            ->andWhere('t.email = :email')
            ->setParameter(':email', $email)
            ->execute()->fetchColumn(0) > 0;
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
        return $this->connection->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(self::TABLE_NAME, 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'n.user_id = u.id')
            ->andWhere('n.network = :network and n.identity = :identity')
            ->setParameter(':network', $network)
            ->setParameter(':identity', $identity)
            ->execute()->fetchColumn(0) > 0;
    }

    /**
     * Возвращает пользователя по email для аутентификации
     *
     * @param string $email
     * @return AuthView|null
     */
    public function findForAuthByEmail(string $email): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'email', 'password_hash', 'role', 'status')
            ->from(self::TABLE_NAME)
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function findForAuthByNetwork(string $network, string $identity): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('u.id', 'u.email', 'u.password_hash', 'u.role', 'u.status')
            ->from(self::TABLE_NAME, 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'n.user_id = u.id')
            ->where('n.network = :network and n.identity = :identity')
            ->setParameter(':network', $network)
            ->setParameter(':identity', $identity)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Проверка суещствования токена для сброса пароля
     *
     * @param string $token
     * @return bool
     */
    public function existByResetToken(string $token)
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT(t.id)')
            ->from(self::TABLE_NAME, 't')
            ->andWhere('t.reset_token = :token')
            ->setParameter(':token', $token)
            ->execute()->fetchColumn(0) > 0;
    }
}