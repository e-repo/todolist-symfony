<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Read\Filter;

class ListFilter
{
    public ?string $name = null;
    public ?string $email = null;
    public ?string $role = null;
    public ?string $status = null;

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
