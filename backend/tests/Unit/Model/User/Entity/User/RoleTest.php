<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User;

use App\Domain\Auth\User\Entity\User\Role;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function testSuccess(): void
    {
        $userBuilder = new UserBuilder();
        $user = $userBuilder
            ->confirmed()
            ->buildByEmail();

        $user->changeRole(Role::admin());

        self::assertFalse($user->getRole()->isUser());
        self::assertTrue($user->getRole()->isAdmin());
    }

    public function testAlready(): void
    {
        $userBuilder = new UserBuilder();
        $user = $userBuilder
            ->confirmed()
            ->buildByEmail();

        $this->expectExceptionMessage('Role is already same.');
        $user->changeRole(Role::user());
    }
}