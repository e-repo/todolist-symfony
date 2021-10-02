<?php

declare(strict_types=1);

namespace App\ReadModel\Task\FilterBar;

use App\Model\Todos\Entity\Task\Task;
use Webmozart\Assert\Assert;

class Filter
{
    public string $userId;
    public string $status = Task::STATUS_PUBLISHED;
    public string $date = '';
    public string $sort = 'date';
    public string $direction = 'desc';

    public function __construct($userId)
    {
        Assert::notEmpty($userId);
        $this->userId = $userId;
    }
}