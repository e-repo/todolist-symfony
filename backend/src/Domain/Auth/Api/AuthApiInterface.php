<?php

namespace App\Domain\Auth\Api;

use App\Domain\Auth\Api\Dto\UserPresenterDto;

interface AuthApiInterface
{
    public function getByUuid(string $uuid): UserPresenterDto;
}