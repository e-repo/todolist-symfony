<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Network;

use App\Domain\Auth\Entity\User\Network;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    public function testSuccess(): void
    {
        $userBuilder = new UserBuilder();
        $user = $userBuilder
            ->buildByNetwork();

        self::assertTrue($user->isActive());
        self::assertFalse($user->isBlocked());
        self::assertFalse($user->isWait());

        self::assertNull($user->getEmail());
        self::assertNull($user->getConfirmToken());

        self::assertEquals($user->getId(), $userBuilder->defaultParams('id'));
        self::assertEquals($user->getDate(), $userBuilder->defaultParams('date'));

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = reset($networks));
        self::assertEquals($userBuilder->getNetwork(), $first->getNetwork());
        self::assertEquals($userBuilder->getIdentity(), $first->getIdentity());
    }
}