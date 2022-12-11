<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\Read\Filter;

class TaskFilter
{
    public string $userId;
    public ?string $status = null;
    public string $name = '';
    public string $description = '';
    public string $date = '';
    public string $sort = 'date';
    public string $direction = 'desc';

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function setSort(string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function setDirection(string $direction): self
    {
        $this->direction = $direction;

        return $this;
    }
}
