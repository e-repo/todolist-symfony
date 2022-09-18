<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Entity\User\Type;

use App\Domain\Auth\User\Entity\User\Role;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class RoleType extends StringType
{
    public const NAME = 'user_user_role';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Role ? $value->name() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Role
    {
        return ! empty($value) ? new Role($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}