<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Dto;

class Image
{
    private string $uuid;
    private string $filename;
    private string $originFilename;
    private string $filepath;
    private bool $isActive;

    public function __construct(
        string $uuid,
        string $filename,
        string $originFilename,
        string $filepath,
        bool $isActive
    )
    {
        $this->uuid = $uuid;
        $this->filename = $filename;
        $this->originFilename = $originFilename;
        $this->filepath = $filepath;
        $this->isActive = $isActive;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getOriginFilename(): string
    {
        return $this->originFilename;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
