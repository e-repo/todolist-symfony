<?php

declare(strict_types=1);

namespace App\Security;

use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var UserFetcher
     */
    private UserFetcher $users;

    /**
     * UserProvider constructor.
     * @param UserFetcher $users
     */
    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername($username)
    {
        // TODO: Implement loadUserByUsername() method.
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user)
    {
        // TODO: Implement refreshUser() method.
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class)
    {
        // TODO: Implement supportsClass() method.
    }
}