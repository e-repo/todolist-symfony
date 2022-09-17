<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Entity\User;

use Webmozart\Assert\Assert;

class Role
{
    public const USER = 'ROLE_USER';
    public const ADMIN = 'ROLE_ADMIN';

    private string $value;

    /**
     * Role constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::USER,
            self::ADMIN
        ]);

        $this->value = $name;
    }

    /**
     * Возвращает объект Role с
     * установленной ролью self::USER
     *
     * @return self
     */
    public static function user(): self
    {
        return new self(self::USER);
    }
    /**
     * Возвращает объект Role с
     * установленной ролью self::ADMIN
     *
     * @return self
     */
    public static function admin(): self
    {
        return new self(self::ADMIN);
    }
    /**
     * Проверка на роль USER_ROLE
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->value === self::USER;
    }
    /**
     * Проверка на роль USER_ADMIN
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->value === self::ADMIN;
    }
    /**
     * Проверка роли на соответствие
     *
     * @param Role $role
     * @return bool
     */
    public function isEqual(self $role): bool
    {
        return $role->name() === $this->value;
    }
    /**
     * Возвращает наименование роли
     *
     * @return string
     */
    public function name(): string
    {
        return $this->value;
    }
}