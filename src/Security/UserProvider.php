<?php

declare(strict_types=1);

namespace App\Security;

use App\ReadModel\User\AuthView;
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
    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->loadUser($username);
        return $this->identityByUser($user, $username);
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $identity): UserInterface
    {
        if (! $identity instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class' . get_class($identity));
        }

        $user = $this->loadUser($identity->getUsername());
        return $this->identityByUser($user, $identity->getUsername());
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class): bool
    {
        return $class === UserIdentity::class;
    }

    private function identityByUser(AuthView $user, string $username): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $username,
            $user->password_hash ?: '',
            $user->role,
            $user->status
        );
    }

    private function loadUser($username): AuthView
    {
        $chunks = explode(':', $username);

        if (count($chunks) === 2 && $user = $this->users->findForAuthByNetwork($chunks[0], $chunks[1])) {
            return $user;
        }

        if ($user = $this->users->findForAuthByEmail($username)) {
            return $user;
        }

        throw new UsernameNotFoundException('');
    }
}