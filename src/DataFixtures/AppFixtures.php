<?php

namespace App\DataFixtures;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\PasswordHasher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    const ADMIN_PASSWORD = 'secret';
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
