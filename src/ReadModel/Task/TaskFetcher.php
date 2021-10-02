<?php

declare(strict_types=1);

namespace App\ReadModel\Task;

use App\Model\Todos\Entity\Task\Task;
use App\ReadModel\Task\Filter\Filter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\UnexpectedResultException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class TaskFetcher extends ServiceEntityRepository
{
    private Connection $connection;
    private PaginatorInterface $paginator;

    /**
     * TaskFetcher constructor.
     * @param ManagerRegistry $registry
     * @param Connection $connection
     * @param PaginatorInterface $paginator
     */
    public function __construct(ManagerRegistry $registry, Connection $connection, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Task::class);
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    /**
     * Возвращает число задач пользователя
     *
     * @param string $userId
     * @return int|null
     */
    public function numberUserTasks(string $userId): ?int
    {
        try {
            $result = $this->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->where('t.user = :userId')
                ->setParameter(':userId', $userId)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return null;
        }

        return (int)$result;
    }

    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name',
                'description',
                'status',
                'date'
            )
            ->where('user_id = :userId')
            ->setParameter(':userId', $filter->userId)
            ->from('todos_tasks');

        if ($name = $filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(name)', ':name'));
            $qb->setParameter(':name', $name);
        }

        if ($description = $filter->description) {
            $qb->andWhere($qb->expr()->like('LOWER(description)', ':description'));
            $qb->setParameter(':description', $description);
        }

        if ($status = $filter->status) {
            $qb->andWhere('status = :status');
            $qb->setParameter(':status', $status);
        }

        if ($date = $filter->date) {
            $dateTime = \DateTimeImmutable::createFromFormat('d.m.Y H:i:s', sprintf('%s 00:00:00', $date));
            $qb->andWhere('date BETWEEN :dateStart AND :dateEnd');
            $qb->setParameter(':dateStart', $dateTime->format('Y-m-d H:i:s'));
            $qb->setParameter(':dateEnd', $dateTime->modify('+1 day')->format('Y-m-d H:i:s'));
        }

        if (! \in_array($sort, ['name', 'description', 'status', 'date'])) {
            throw new \UnexpectedValueException(sprintf('Cannot sort by %s', $sort));
        }

        $qb->orderBy($sort, $direction === 'desc' ? $direction : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}