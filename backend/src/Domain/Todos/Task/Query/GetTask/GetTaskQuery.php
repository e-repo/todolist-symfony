<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\Query\GetTask;

class GetTaskQuery
{
    private string $taskUuid;

    public function __construct(
        string $taskUuid
    )
    {
        $this->taskUuid = $taskUuid;
    }

    public function getTaskUuid(): string
    {
        return $this->taskUuid;
    }
}
