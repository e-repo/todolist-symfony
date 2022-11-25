<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Error\Image;

class ImageAlreadyActiveException extends \DomainException
{
    private const DEFAULT_MESSAGE = 'Image already is active';
    private string $userUuid;

    public function __construct(string $imageUuid)
    {
        parent::__construct(self::DEFAULT_MESSAGE);
        $this->userUuid = $imageUuid;
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }
}
