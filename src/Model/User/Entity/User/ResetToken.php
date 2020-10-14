<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * Class ResetToken
 * @package App\Model\User\Entity\User
 * @ORM\Embeddable()
 */
class ResetToken
{
    /**
     * @ORM\Column(type="string", name="token", nullable=true)
     */
    private string $value;
    /**
     * @ORM\Column(type="date_immutable", name="token_expires", nullable=true)
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