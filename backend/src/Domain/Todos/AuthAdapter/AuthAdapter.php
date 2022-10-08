<?php

declare(strict_types=1);

namespace App\Domain\Todos\AuthAdapter;

use App\Domain\Auth\Api\AuthApiInterface;
use App\Domain\Todos\AuthAdapter\Dto\UserDto;

class AuthAdapter
{
    private AuthApiInterface $authApi;

    /**
     * @param AuthApiInterface $authApi
     */
    public function __construct(
        AuthApiInterface $authApi
    )
    {
        $this->authApi = $authApi;
    }

    public function getUserByUuid(string $uuid): UserDto
    {
        $user = $this->authApi->getByUuid($uuid);
        // Todo
    }
}