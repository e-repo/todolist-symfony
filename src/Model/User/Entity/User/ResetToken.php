<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Webmozart\Assert\Assert;

class ResetToken
{
    /**
     * @var string
     */
    private string $value;
    /**
     * @var \DateTimeImmutable
     */
    private \DateTimeImmutable $expires;

    /**
     * ResetToken constructor.
     * @param string $token
     * @param \DateTimeImmutable $expires
     */
    public function __construct(string $token, \DateTimeImmutable $expires)
    {
        Assert::notEmpty($token);
        $this->value = $token;
        $this->expires = $expires;
    }

    /**
     * @param \DateTimeImmutable $date
     * @return bool
     */
    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->value;
    }

    public function isEmpty()
    {
        return empty($this->token);
    }
}