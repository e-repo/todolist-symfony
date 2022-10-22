<?php

namespace App\Domain\Auth\Api;

use App\Domain\Auth\Api\Dto\UserPresenterDto;

interface AuthApiInterface
{
    public function findByUuid(string $uuid): UserPresenterDto;
}