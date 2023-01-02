<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\Dto;

class TaskDto
{
    private string $uuid;
    private string $userUuid;
    private string $name;
    private string $status;
    private \DateTimeImmutable $date;
    private ?string $description = null;

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setUuid(string $uuid): TaskDto
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function setUserUuid(string $userUuid): TaskDto
    {
        $this->userUuid = $userUuid;
        return $this;
    }

    public function setName(string $name): TaskDto
    {
        $this->name = $name;
        return $this;
    }

    public function setStatus(string $status): TaskDto
    {
        $this->status = $status;
        return $this;
    }

    public function setDescription(?string $description): TaskDto
    {
        $this->description = $description;
        return $this;
    }

    public function setDate(\DateTimeImmutable $date): TaskDto
    {
        $this->date = $date;
        return $this;
    }
}
