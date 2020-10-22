<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\User\Entity\User\User;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface, EquatableInterface
{
    private string $id;
    private string $userName;
    private string $password;
    private string $role;
    private string $status;

    /**
     * UserIdentity constructor.
     * @param string $id
     * @param string $userName
     * @param string $password
     * @param string $role
     * @param string $status
     */
    public function __construct(
        string $id,
        string $userName,
        string $password,
        string $role,
        string $status
    )
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->password = $password;
        $this->role = $role;
        $this->status = $status;
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if (! $user instanceof self) {
            return false;
        }

        return $this->id === $user->id &&
            $this->userName === $user->userName &&
            $this->password === $user->password &&
            $this->role === $user->role &&
            $this->status === $user->status;
    }

    /**
     * @return mixed
     */
    public function getRoles(): array
    {
        return [$this->role];
    }

    /**
     * @return string|null
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->userName;
    }

    /**
     * Проверка статуса пльзователя
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === User::STATUS_ACTIVE;
    }

    /**
     * @return mixed
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}