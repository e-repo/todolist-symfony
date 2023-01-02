<?php

namespace App\Domain\Auth\Api;

use App\Domain\Auth\Api\Dto\UserPresenterDto;
use App\Domain\Service\Exception\EntityNotFoundException;

interface AuthApiInterface
{

    /**
     * @param string $uuid
     * @return UserPresenterDto
     * @throws EntityNotFoundException
     */
    public function getByUuid(string $uuid): UserPresenterDto;
}
