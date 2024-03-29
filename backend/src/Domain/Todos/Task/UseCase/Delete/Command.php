<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\UseCase\Delete;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank
     */
    public string $taskUuid;

    public function __construct(string $taskUuid)
    {
        $this->taskUuid = $taskUuid;
    }
}
