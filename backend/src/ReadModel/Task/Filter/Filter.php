<?php

declare(strict_types=1);

namespace App\ReadModel\Task\Filter;

use App\Model\Todos\Entity\Task\Task;
use Webmozart\Assert\Assert;

class Filter
{
    public string $userId;
    public ?string $name = null;
    public ?string $description = null;
    public ?string $status = null;
    public ?string $date = null;

    public function __construct(string $userId, string $status = null)
    {
        Assert::notEmpty($userId);
        $this->userId = $userId;
        $this->status = $status;
    }
}