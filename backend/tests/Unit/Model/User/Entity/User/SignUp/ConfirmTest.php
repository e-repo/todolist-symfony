<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Domain\User\Entity\User\Email;
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
        self::assertFalse($user->isBlocked());
        self::assertFalse($user->isWait());

        self::assertEquals($user->getId(), $userBuilder->defaultParams('id'));
        self::assertEquals($user->getEmail(), new Email($userBuilder->defaultParams('email')));
        self::assertEquals($user->getDate(), $userBuilder->defaultParams('date'));
        self::assertEquals($user->getPasswordHash(), $userBuilder->defaultParams('passwordHash'));
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