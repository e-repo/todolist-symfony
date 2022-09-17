<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Service;

class PasswordHasher
{
    /**
     * @param string $password
     * @return string
     */
    public function hash(string $password): string
    {
        $hash = password_hash($password, PASSWORD_ARGON2I);
        if ($hash === false) {
            throw new \RuntimeException('Unable to generate hash.');
        }
        return $hash;
    }

    public function hashRandom(): string
    {
        $someNumber = rand(100000, 1000000);
        return $this->hash((string) $someNumber);
    }

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function validate(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}