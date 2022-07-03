<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Reset;

use App\Domain\Auth\Entity\User\ResetToken;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class ResetTest extends TestCase
{
    public function TestSuccess()
    {
        $newHashPassword = 'hash1';
        $now = new \DateTimeImmutable();
        $userBuilder = new UserBuilder();
        $token = new ResetToken(
            $userBuilder->defaultParams('token'),
            $now->modify('+1 day')
        );
        $user = $userBuilder
            ->confirmed()
            ->buildByEmail();

        $user->requestPasswordReset($token, $now);
        self::assertNotNull($user->getRequestToken());

        $user->passwordReset($now, $newHashPassword);
        self::assertNull($user->getRequestToken());
        self::assertEquals($newHashPassword, $user->getPasswordHash());
    }

    public function testExpiredToken()
    {
        $newHashPassword = 'hash1';
        $userBuilder = new UserBuilder();
        $now = new \DateTimeImmutable();
        $token = new ResetToken($userBuilder->defaultParams('token'), $now->modify('+1 day'));
        $user = $userBuilder
            ->confirmed()
            ->buildByEmail();

        $user->requestPasswordReset($token, $now);
        self::expectExceptionMessage('Reset token is expired.');
        $user->passwordReset($now->modify('+2 day'), $newHashPassword);
    }

    public function testNotRequested(): void
    {
        $newHashPassword = 'hash1';
        $userBuilder = new UserBuilder();
        $now = new \DateTimeImmutable();
        $user = $userBuilder
            ->confirmed()
            ->buildByEmail();

        self::expectExceptionMessage('Resisting is not requested.');
        $user->passwordReset($now, $newHashPassword);
    }
}