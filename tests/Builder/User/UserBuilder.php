<?php

declare(strict_types=1);

namespace App\Tests\Builder\User;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;

class UserBuilder
{
    private Id $id;
    private \DateTimeImmutable $date;

    private ?Email $email;
    private ?string $hash;
    private ?string $token;

    private bool $confirmed = false;

    private ?string $network;
    private ?string $identity;

    /**
     * UserBuilder constructor.
     */
    public function __construct()
    {
        $this->id       = Id::next();
        $this->date     = new \DateTimeImmutable();
        $this->email    = new Email('test@test.ru');
        $this->hash     = 'hash';
        $this->token    = 'token';
        $this->network  = 'vk';
        $this->identity = '0000001';
    }

    public function confirmed(): self
    {
        $clone = clone $this;
        $clone->confirmed = true;
        return $clone;
    }
    /**
     * @param Email|null $email
     * @param string|null $hash
     * @param string|null $token
     * @return $this
     */
    public function viaEmail(
        Email $email = null,
        string $hash = null,
        string $token = null
    ): self
    {
        $clone = clone $this;
        $clone->email = $email ?? $this->email;
        $clone->hash = $hash ?? $this->hash;
        $clone->token = $token ?? $this->token;
        return $clone;
    }

    /**
     * @param string|null $network
     * @param string|null $identity
     * @return $this
     */
    public function viaNetwork(string $network = null, string $identity = null): self
    {
        $clone = clone $this;
        $clone->network = $network ?? $this->network;
        $clone->identity = $identity ?? $this->identity;
        return $clone;
    }

    /**
     * @return User
     */
    public function buildByEmail(): User
    {
        $user = User::signUpByEmail(
            $this->id,
            $this->date,
            $this->email,
            $this->hash,
            $this->token
        );

        if ($this->confirmed) {
            $user->confirmSignUp();
        }

        return $user;
    }

    public function buildByNetwork(): User
    {
        return User::signUpByNetwork(
            $this->id,
            $this->date,
            $this->network,
            $this->identity
        );
    }

    /**
     * @return Id|null
     */
    public function getId(): ?Id
    {
        return $this->id;
    }

    /**
     * @return Id|null
     */
    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return Email|null
     */
    public function getEmail(): ?Email
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return string|null
     */
    public function getNetwork(): ?string
    {
        return $this->network;
    }

    /**
     * @return string|null
     */
    public function getIdentity(): ?string
    {
        return $this->identity;
    }
}