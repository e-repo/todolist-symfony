<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Read\Filter;

class Filter
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $role = null;
    public ?string $status = null;

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }
}