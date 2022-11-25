<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\User\Response;

class ImageResponseDto
{
    public string $uuid;
    public string $filename;
    public string $originalFilename;
    public string $filepath;
    public bool $isActive;

    /**
     * @param string $uuid
     * @return ImageResponseDto
     */
    public function setUuid(string $uuid): ImageResponseDto
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function setFilename(string $filename): ImageResponseDto
    {
        $this->filename = $filename;
        return $this;
    }

    public function setOriginalFilename(string $originalFilename): ImageResponseDto
    {
        $this->originalFilename = $originalFilename;
        return $this;
    }

    public function setFilepath(string $filepath): ImageResponseDto
    {
        $this->filepath = $filepath;
        return $this;
    }

    public function setIsActive(bool $isActive): ImageResponseDto
    {
        $this->isActive = $isActive;
        return $this;
    }
}
