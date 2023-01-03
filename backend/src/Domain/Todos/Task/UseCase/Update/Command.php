<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\UseCase\Update;

use App\Domain\Todos\Task\Entity\Task\Task;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank
     */
    public string $taskUuid;
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
        $this->taskUuid = $task->getId()->getValue();
        $this->name = $task->getName();
        $this->description = $task->getDescription();
    }

    public function setTaskUuid(string $taskUuid): self
    {
        $this->taskUuid = $taskUuid;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
