<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $userBuilder = new UserBuilder();

        $user = $userBuilder
            ->viaEmail()
            ->buildByEmail();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isNew());
        self::assertFalse($user->isActive());

        self::assertEquals($user->getId(), $userBuilder->getId());
        self::assertEquals($user->getEmail(), $userBuilder->getEmail());
        self::assertEquals($user->getDate(), $userBuilder->getDate());
        self::assertEquals($user->getPasswordHash(), $userBuilder->getHash());
        self::assertEquals($user->getConfirmToken(), $userBuilder->getToken());

        self::assertTrue($user->getRole()->isUser());
    }
}