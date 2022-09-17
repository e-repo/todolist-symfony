<?php

declare(strict_types=1);

namespace App\Tests\Builder\User;

use App\Domain\Auth\User\Entity\User\Email;
use App\Domain\Auth\User\Entity\User\Id;
use App\Domain\Auth\User\Entity\User\Name;
use App\Domain\Auth\User\Entity\User\User;

class UserBuilder
{
    private Id $id;
    private \DateTimeImmutable $date;
    private Name $name;

    private ?Email $email = null;
    private ?string $hash = null;
    private ?string $token = null;

    private bool $confirmed = false;

    private ?string $network = null;
    private ?string $identity = null;

    /**
     * UserBuilder constructor.
     */
    public function __construct()
    {
        $this->id       = Id::next();
        $this->date     = new \DateTimeImmutable();
        $this->name     = new Name(
            $this->defaultParams('firstName'),
            $this->defaultParams('lastName')
        );
    }

    /**
     * Возвращает параметры сущности User
     *
     * @param string $paramsName
     * @return mixed
     */
    public function defaultParams(string $paramsName)
    {
        $paramsList = self::paramsList();

        if (!isset($paramsList[$paramsName])) {
            throw new \DomainException(sprintf("Param '%s' not set.", $paramsName));
        }

        return $paramsList[$paramsName];
    }

    public function confirmed(): self
    {
        $clone = clone $this;
        $clone->confirmed = true;
        return $clone;
    }

    /**
     * @return User
     */
    public function buildByEmail(): User
    {
        $this->viaEmail();
        $user = User::signUpByEmail(
            $this->id,
            $this->date,
            $this->name,
            $this->email,
            $this->hash,
            $this->token
        );

        if ($this->confirmed) {
            $user->confirmSignUp();
        }

        return $user;
    }

    public function buildManual(): User
    {
        $this->manual();
        return User::signUpByManual(
            $this->id,
            $this->date,
            $this->name,
            $this->email,
            $this->hash
        );
    }

    public function buildByNetwork(): User
    {
        $this->viaNetwork();
        return User::signUpByNetwork(
            $this->id,
            $this->date,
            $this->name,
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
     * @return Name|null
     */
    public function getName(): ?Name
    {
        return $this->name;
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

    /**
     * @param Email|null $email
     * @param string|null $hash
     * @param string|null $token
     * @return $this
     */
    private function viaEmail(Email $email = null, string $hash = null, string $token = null): self
    {
        $this->email = $email ?? new Email($this->defaultParams('email'));
        $this->hash = $hash ?? $this->defaultParams('passwordHash');
        $this->token = $token ?? $this->defaultParams('token');
        return $this;
    }

    /**
     * @param Email|null $email
     * @param string|null $hash
     * @return void
     */
    private function manual(Email $email = null, string $hash = null): void
    {
        $this->email = $email ?? new Email($this->defaultParams('email'));
        $this->hash = $hash ?? $this->defaultParams('passwordHash');
    }

    /**
     * @param string|null $network
     * @param string|null $identity
     * @return $this
     */
    private function viaNetwork(string $network = null, string $identity = null): self
    {
        $this->network = $network ?? $this->defaultParams('network');
        $this->identity = $identity ?? $this->defaultParams('identity');
        return $this;
    }

    /**
     * Возвращает дефолтные параметры
     * для сущности User
     *
     * @return array
     */
    private function paramsList(): array
    {
        return [
            'id'            => $this->id,
            'date'          => $this->date,
            'firstName'     => 'Firstname',
            'lastName'      => 'Lastname',
            'email'         => 'test@test.ru',
            'passwordHash'  => 'hash',
            'token'         => 'token',
            'network'       => 'vk',
            'identity'      => '0000001'
        ];
    }
}