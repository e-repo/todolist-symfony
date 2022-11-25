<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Read\Filter;

class ImageListFilter
{
    private string $uuid;
    private bool $onlyInactive;

    public function setUuid(string $uuid): ImageListFilter
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setOnlyInactive(bool $onlyInactive): ImageListFilter
    {
        $this->onlyInactive = $onlyInactive;
        return $this;
    }

    public function isOnlyInactive(): bool
    {
        return $this->onlyInactive;
    }
}
