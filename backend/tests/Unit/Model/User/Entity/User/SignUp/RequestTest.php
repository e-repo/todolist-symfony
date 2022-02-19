<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Email;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $userBuilder = new UserBuilder();

        $user = $userBuilder
            ->buildByEmail();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isBlocked());
        self::assertFalse($user->isActive());

        self::assertEquals($user->getId(), $userBuilder->defaultParams('id'));
        self::assertEquals($user->getName(), $userBuilder->getName());
        self::assertEquals($user->getEmail(), $userBuilder->getEmail());
        self::assertEquals($user->getDate(), $userBuilder->defaultParams('date'));
        self::assertEquals($user->getPasswordHash(), $userBuilder->defaultParams('passwordHash'));
        self::assertEquals($user->getConfirmToken(), $userBuilder->defaultParams('token'));

        self::assertTrue($user->getRole()->isUser());
    }
}