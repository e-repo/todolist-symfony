<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\Read\Filter;

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