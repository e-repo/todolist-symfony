<?php

declare(strict_types=1);

namespace App\Domain\Auth\Api;

use App\Domain\Auth\Api\Dto\UserPresenterDto;

class AuthApi implements AuthApiInterface
{

    public function getByUuid(string $uuid): UserPresenterDto
    {
        // TODO: Implement getByUuid() method.
    }
}