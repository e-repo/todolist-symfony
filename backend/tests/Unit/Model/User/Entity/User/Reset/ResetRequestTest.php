<?php

declare(strict_types=1);

namespace App\Test\Unit\Model\User\Entity\User\Reset;

use App\Domain\User\Entity\User\ResetToken;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class ResetRequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $userBuilder = new UserBuilder();

        $user = $userBuilder
            ->confirmed()
            ->buildByEmail();

        $user->requestPasswordReset($token, $now);

        self::assertTrue($user->isActive());
        self::assertFalse($user->isBlocked());
        self::assertFalse($user->isWait());

        self::assertNotNull($user->getRequestToken());
    }

    public function testAlready(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $userBuilder = new UserBuilder();

        $user = $userBuilder
            ->confirmed()
            ->buildByEmail();

        $user->requestPasswordReset($token, $now);
        self::expectExceptionMessage('Resetting is already request.');
        $user->requestPasswordReset($token, $now);
    }

    public function testExpired(): void
    {
        $now = new \DateTimeImmutable();
        $userBuilder = new UserBuilder();

        $user = $userBuilder
            ->confirmed()
            ->buildByEmail();

        $token1 = new ResetToken('token', $now->modify('+1 day'));
        $token2 = new ResetToken('token', $now->modify('+3 day'));

        // Устанавливаем токен со истечением через день
        $user->requestPasswordReset($token1, $now);
        self::assertEquals($token1, $user->getRequestToken());

        // Через день устанавливаем новый токен с истечением через день
        $user->requestPasswordReset($token2, $now->modify('+2 day'));
        self::assertEquals($token2, $user->getRequestToken());
    }

    public function testWithoutConfirmed(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $userBuilder = new UserBuilder();

        $user = $userBuilder
            ->buildByEmail();

        self::expectExceptionMessage('User is not active.');
        $user->requestPasswordReset($token, $now);
    }

    public function testWithoutEmail(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $userBuilder = new UserBuilder();

        $user = $userBuilder
            ->buildByNetwork();

        self::expectExceptionMessage('Email is not specified.');
        $user->requestPasswordReset($token, $now);
    }
}