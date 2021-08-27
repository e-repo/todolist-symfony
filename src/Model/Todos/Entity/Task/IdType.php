<?php

declare(strict_types=1);

namespace App\Model\Todos\Entity\Task;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class IdType extends GuidType
{
    public const NAME = 'todos_task_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof Id ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Id
    {
        return \is_string($value) ? new Id($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}