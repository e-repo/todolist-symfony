<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Read;

use App\Domain\Auth\User\Read\Filter\ImageListFilter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ImageFetcher
{
    private Connection $connection;
    private PaginatorInterface $paginator;

    /**
     * @param Connection $connection
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        Connection $connection,
        PaginatorInterface $paginator
    )
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function allByFilter(
        ImageListFilter $filter,
        int $page,
        int $size,
        string $sort,
        string $direction
    ): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id as uuid',
                'filename',
                'original_filename',
                'filepath',
                'is_active'
            )
            ->from('user_image')
            ->where('user_id = :uuid')
            ->setParameter(':uuid', $filter->getUuid());

        if (true === $filter->isOnlyInactive()) {
            $qb
                ->andWhere('is_active = :is_active')
                ->setParameter(':is_active', false, ParameterType::BOOLEAN);
        }

        if (! \in_array($sort, ['original_filename', 'created_at'], true)) {
            throw new \UnexpectedValueException(sprintf('Cannot sort by %s', $sort));
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}
