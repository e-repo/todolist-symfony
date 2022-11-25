<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Error\Image;

class ImageNotBelongsToUserException extends \DomainException
{
    private const DEFAULT_MESSAGE = 'Image does\'t belongs to user';
    private string $userUuid;

    public function __construct(string $userUuid)
    {
        parent::__construct(self::DEFAULT_MESSAGE);
        $this->userUuid = $userUuid;
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }
}
