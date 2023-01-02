<?php

declare(strict_types=1);

namespace App\Domain\Todos\AuthAdapter;

use App\Domain\Auth\Api\AuthApiInterface;
use App\Domain\Service\Exception\EntityNotFoundException;
use App\Domain\Todos\AuthAdapter\Dto\UserDto;
use App\Domain\Todos\AuthAdapter\Exception\UserNotFoundException;

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

    /**
     * @param string $uuid
     * @return UserDto
     * @throws UserNotFoundException
     */
    public function getUserByUuid(string $uuid): UserDto
    {
        try {
            $user = $this->authApi->getByUuid($uuid);
        } catch (EntityNotFoundException $e) {
            throw new UserNotFoundException($e->getMessage(), $uuid);
        }

        return new UserDto(
            $user->getId(),
            $user->getDate(),
            $user->getRole(),
            $user->getStatus(),
            $user->getNetworks(),
            $user->getName()->getFirst(),
            $user->getName()->getLast(),
            $user->getActiveImageUuid(),
            $user->getEmail()
        );
    }
}
