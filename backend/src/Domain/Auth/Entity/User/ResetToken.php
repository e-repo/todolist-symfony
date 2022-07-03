<?php

declare(strict_types=1);

namespace App\Domain\Auth\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * Class ResetToken
 * @package App\Domain\User\Entity\User
 * @ORM\Embeddable()
 */
class ResetToken
{
    /**
     * @ORM\Column(type="string", name="token", nullable=true)
     */
    private string $token;
    /**
     * @ORM\Column(type="datetime_immutable", name="token_expires", nullable=true)
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
        $this->token = $token;
        $this->expires = $expires;
    }

    /**
     * Истек или нет?
     *
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
        return $this->token;
    }

    public function isEmpty()
    {
        return empty($this->token);
    }
}