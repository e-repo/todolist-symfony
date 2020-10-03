<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;


use Webmozart\Assert\Assert;

class Role
{
    private const USER = 'ROLE_USER';
    private const ADMIN = 'ROLE_ADMIN';

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

    public static function user(): self
    {
        return new self(self::USER);
    }

    public static function admin(): self
    {
        return new self(self::ADMIN);
    }

    public function isEqual(self $role): bool
    {
        return $role->name() === $this->value;
    }

    public function name(): string
    {
        return $this->value;
    }
}