<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\Read;

use App\Domain\Todos\Task\Entity\Task\Task;
use App\Domain\Todos\Task\Read\Filter\TaskFilter;
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
                ->where('t.userId = :userId')
                ->setParameter(':userId', $userId)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return null;
        }

        return (int)$result;
    }

    public function allByFilter(TaskFilter $filter, int $page, int $size): PaginationInterface
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
            ->andWhere('deleted_at IS NULL')
            ->setParameter(':userId', $filter->userId)
            ->from('task');

        if ($status = $filter->status) {
            $qb->andWhere('status = :status');
            $qb->setParameter(':status', $status);
        }

        if ('' !== $filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(name)', ':name'));
            $qb->setParameter(':name', $filter->name);
        }

        if ('' !== $filter->date) {
            $dateTime = \DateTimeImmutable::createFromFormat('d.m.Y H:i:s', sprintf('%s 00:00:00', $filter->date));
            $qb->andWhere('date BETWEEN :dateStart AND :dateEnd');
            $qb->setParameter(':dateStart', $dateTime->format('Y-m-d H:i:s'));
            $qb->setParameter(':dateEnd', $dateTime->modify('+1 day')->format('Y-m-d H:i:s'));
        }

        if ($description = $filter->description) {
            $qb->andWhere($qb->expr()->like('LOWER(description)', ':description'));
            $qb->setParameter(':description', $description);
        }

        if (! \in_array($filter->sort, ['status', 'date'])) {
            throw new \UnexpectedValueException(sprintf('Cannot sort by %s', $filter->sort));
        }

        $qb->orderBy($filter->sort, $filter->direction === 'desc' ? $filter->direction : 'asc');
        return $this->paginator->paginate($qb, $page, $size);
    }
}
