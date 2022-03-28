<?php

namespace App\Infrastructure\DataFixtures;

use App\Domain\User\Entity\User\Email;
use App\Domain\User\Entity\User\Id;
use App\Domain\User\Entity\User\Name;
use App\Domain\User\Entity\User\Role;
use App\Domain\User\Entity\User\User;
use App\Domain\User\Service\PasswordHasher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private const FIRST_NAME = 'Admin';
    private const LAST_NAME = 'Adminov';
    private const ADMIN_PASSWORD = 'secret';
    const ADMIN_EMAIL = 'admin@test.ru';

    /**
     * @var PasswordHasher
     */
    private PasswordHasher $hasher;

    /**
     * AppFixtures constructor.
     * @param PasswordHasher $hasher
     */
    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $passwordHash = $this->hasher->hash(self::ADMIN_PASSWORD);

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Name(self::FIRST_NAME, self::LAST_NAME),
            new Email(self::ADMIN_EMAIL),
            $passwordHash,
            'token'
        );

        $user->confirmSignUp();

        $user->changeRole(Role::admin());

        $manager->persist($user);
        $manager->flush();
    }
}
