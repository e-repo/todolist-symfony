<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    public function testSuccess()
    {
        $userBuilder = new UserBuilder();

        $user = $userBuilder
            ->confirmed()
            ->buildByEmail();

        self::assertTrue($user->isActive());
        self::assertFalse($user->isNew());
        self::assertFalse($user->isWait());

        self::assertEquals($user->getId(), $userBuilder->getId());
        self::assertEquals($user->getEmail(), $userBuilder->getEmail());
        self::assertEquals($user->getDate(), $userBuilder->getDate());
        self::assertEquals($user->getPasswordHash(), $userBuilder->getHash());
        self::assertEquals($user->getConfirmToken(), null); // После подтверждения email сбрасывается

        self::assertTrue($user->getRole()->isUser());
    }

    public function testAlready()
    {
        $userBuilder = new UserBuilder();

        $user = $userBuilder
            ->confirmed()
            ->buildByEmail();

        self::expectExceptionMessage('User is already confirmed.');
        $user->confirmSignUp();
    }
}