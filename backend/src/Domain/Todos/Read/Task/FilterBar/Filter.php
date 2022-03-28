<?php

declare(strict_types=1);

namespace App\Domain\Todos\Read\Task\FilterBar;

use Webmozart\Assert\Assert;

class Filter
{
    public string $userId;
    public string $status;
    public string $date = '';
    public string $sort = 'date';
    public string $direction = 'desc';

    public function __construct(string $userId, string $status)
    {
        Assert::notEmpty($userId);
        Assert::notEmpty($status);

        $this->userId = $userId;
        $this->status = $status;
    }
}