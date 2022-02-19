<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (! $user instanceof UserIdentity) {
            return;
        }

        if (! $user->isActive()) {
            $exception = new DisabledException('User account is disabled.');
            $exception->setUser($user);
            throw $exception;
        }
    }

    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if (! $user instanceof UserInterface) {
            return;
        }
    }
}