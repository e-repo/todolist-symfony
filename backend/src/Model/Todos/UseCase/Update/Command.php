<?php

declare(strict_types=1);

namespace App\Model\Todos\UseCase\Update;

use App\Model\Todos\Entity\Task\Task;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank
     */
    public string $taskId;
    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    public string $name;

    public ?string $description = null;

    /**
     * @param Task $task
     */
    public function createFromTask(Task $task)
    {
        $this->taskId = $task->getId()->getValue();
        $this->name = $task->getName();
        $this->description = $task->getDescription();
    }
}