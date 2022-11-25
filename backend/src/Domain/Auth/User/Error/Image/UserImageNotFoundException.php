<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Error\Image;

use App\Domain\Service\Exception\AbstractNotFoundException;

class UserImageNotFoundException extends AbstractNotFoundException
{
    private const DEFAULT_MESSAGE = 'Image not found';

    public function getDefaultMessage(): string
    {
        return self::DEFAULT_MESSAGE;
    }
}
