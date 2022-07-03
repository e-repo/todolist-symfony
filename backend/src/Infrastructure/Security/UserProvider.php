<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Auth\Entity\User\Name;
use App\Domain\Auth\Read\AuthView;
use App\Domain\Auth\Read\UserFetcher;
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

    /**
     * @param AuthView $user
     * Не всегда email, если через соцсеть, то
     * <название соцсети>:<идентификатор>
     * @param string $username
     * @return UserIdentity
     */
    private function identityByUser(AuthView $user, string $username): UserIdentity
    {
        $name = $user->firstName && $user->lastName
            ? new Name($user->firstName, $user->lastName)
            : null;

        return new UserIdentity(
            $user->id->getValue(),
            $name,
            $username,
            $user->passwordHash ?: '',
            $user->role->name(),
            $user->status
        );
    }

    /**
     * @param $username
     * @return AuthView
     */
    private function loadUser($username): AuthView
    {
        $chunks = explode(':', $username);

        try {
            if (count($chunks) === 2 && $user = $this->users->findForAuthByNetwork($chunks[0], $chunks[1])) {
                return $user;
            }

            if ($user = $this->users->findForAuthByEmail($username)) {
                return $user;
            }
        } catch (\Doctrine\ORM\NonUniqueResultException $e) {
            throw new UsernameNotFoundException($e->getMessage());
        }

        throw new UsernameNotFoundException();
    }
}