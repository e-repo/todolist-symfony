<?php

declare(strict_types=1);

namespace App\Domain\User\Entity\User;

use Webmozart\Assert\Assert;

class Email
{
    /**
     * @var string
     */
    private string $value = '';

    /**
     * Email constructor.
     * @param string $email
     */
    public function __construct(string $email)
    {
        Assert::notEmpty($email);
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException();
        }
        $this->value = mb_strtolower($email);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqual(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    public function __toString()
    {
        return $this->value;
    }
}