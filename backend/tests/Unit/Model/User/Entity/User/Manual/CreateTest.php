<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Manual;


use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testSuccess(): void
    {
        $userBuilder = new UserBuilder();
        $user = $userBuilder->buildManual();

        self::assertTrue($user->isActive());
        self::assertFalse($user->isBlocked());
        self::assertFalse($user->isWait());

        self::assertEquals($user->getId(), $userBuilder->getId());
        self::assertEquals($user->getName(), $userBuilder->getName());
        self::assertEquals($user->getEmail(), $userBuilder->getEmail());
        self::assertEquals($user->getDate(), $userBuilder->getDate());
        self::assertEquals($user->getPasswordHash(), $userBuilder->getHash());
        self::assertNull($user->getConfirmToken());

        self::assertTrue($user->getRole()->isUser());
    }
}