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
use Faker\Factory;
use Faker\Generator;

class UserFixtures extends Fixture
{
    private const FIRST_NAME = 'Admin';
    private const LAST_NAME = 'Adminov';

    private const ADMIN_PASSWORD = 'secret';
    private const USER_PASSWORD = 'user';

    private const FAKER_LOCALE_RU = 'ru_RU';
    private const COUNT_FAKE_USERS = 100;

    public const ADMIN_EMAIL = 'admin@test.ru';

    /**
     * @var PasswordHasher
     */
    private PasswordHasher $hasher;

    private Generator $faker;

    /**
     * AppFixtures constructor.
     * @param PasswordHasher $hasher
     */
    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
        $this->faker = Factory::create(self::FAKER_LOCALE_RU);
    }

    public function load(ObjectManager $manager)
    {
        $this->persistAdminUser($manager);
        $this->persistFakeUser($manager);

        $manager->flush();
    }

    private function persistAdminUser(ObjectManager $manager): void
    {
        $passwordHash = $this->hasher->hash(self::ADMIN_PASSWORD);

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Name(self::FIRST_NAME, self::LAST_NAME),
            new Email(self::ADMIN_EMAIL),
            $passwordHash,
            'admin_token'
        );

        $user->confirmSignUp();
        $user->changeRole(Role::admin());

        $manager->persist($user);
    }

    private function persistFakeUser(ObjectManager $manager, int $count = self::COUNT_FAKE_USERS): void
    {
        $passwordHash = $this->hasher->hash(self::USER_PASSWORD);

        for ($i = 1; $i <= $count; $i++) {
            $user = User::signUpByEmail(
                Id::next(),
                new \DateTimeImmutable(),
                new Name($this->faker->firstName(), $this->faker->lastName()),
                new Email(\sprintf('user_%s@test.ru', $i)),
                $passwordHash,
                \sprintf('user_%s-token', $i)
            );

            $user->confirmSignUp();
            $manager->persist($user);
        }
    }
}
