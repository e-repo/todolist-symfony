<?php

declare(strict_types=1);

namespace App\Model\Todos\UseCase\Delete;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank
     */
    public string $taskId;

    public function __construct(string $taskId)
    {
        $this->taskId = $taskId;
    }
}