<?php

declare(strict_types=1);

namespace App\Domain\Auth\Api;

use App\Domain\Auth\Api\Dto\UserPresenterDto;
use App\Domain\Auth\User\Repository\UserRepository;
use App\Domain\Service\Exception\EntityNotFoundException;

class AuthApi implements AuthApiInterface
{
    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $uuid
     * @return UserPresenterDto
     * @throws EntityNotFoundException
     */
    public function getByUuid(string $uuid): UserPresenterDto
    {
        $user = $this->userRepository->getByUuid($uuid);

        $activeImageUuid = $user->getActiveImage()
            ? $user->getActiveImage()->getId()
            : null;
        return new UserPresenterDto(
            $user->getId()->getValue(),
            $user->getDate(),
            $user->getRole()->name(),
            $user->getStatus(),
            $user->getNetworks(),
            $user->getName(),
            $activeImageUuid,
            $user->getEmail()->getValue()
        );
    }
}
