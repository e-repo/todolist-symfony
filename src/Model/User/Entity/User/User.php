<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class User
 * @package App\Model\User\Entity\User
 */
class User
{
    private const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';
    private const STATUS_NEW = 'new';

    /**
     * @var Id
     */
    private Id $id;
    /**
     * @var \DateTimeImmutable
     */
    private \DateTimeImmutable $date;
    /**
     * @var Role
     */
    private Role $role;
    /**
     * @var ArrayCollection
     */
    private ArrayCollection $networks;
    /**
     * @var Email
     */
    private Email $email;
    /**
     * @var string
     */
    private string $passwordHash;
    /**
     * Токен для подтвержения пользователя
     * через email, соцсети и тп.
     *
     * @var string | null
     */
    private ?string $confirmToken;
    /**
     * Токен для сброса пароля
     *
     * @var ResetToken | null
     */
    private ?ResetToken $resetToken;
    /**
     * @var string
     */
    private string $status;

    /**
     * User constructor.
     * @param Id $id
     * @param \DateTimeImmutable $date
     */
    private function __construct(Id $id, \DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->date = $date;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    /**
     * Регистрация с потверждением через Email
     * при помощи токена
     *
     * @param Id $id
     * @param \DateTimeImmutable $createdAt
     * @param Email $email
     * @param string $hash
     * @param string $token
     * @return static
     */
    public static function signUpByEmail(Id $id, \DateTimeImmutable $createdAt, Email $email, string $hash, string $token): self
    {
        $user = new self($id, $createdAt);
        $user->email = $email;
        $user->passwordHash = $hash;
        $user->confirmToken = $token;
        $user->status = self::STATUS_WAIT;
        return $user;
    }

    /**
     * Регистрация через соцсети
     *
     * @param Id $id
     * @param \DateTimeImmutable $createdAt
     * @param string $network
     * @param string $identity
     * @return $this
     */
    public function signUpByNetwork(Id $id, \DateTimeImmutable $createdAt, string $network, string $identity): self
    {
        $user = new self($id, $createdAt);
        $user->attachNetwork($network, $identity);
        $user->status = self::STATUS_ACTIVE;
        return $user;
    }

    /**
     * Временный токен, выданный для смены пароля
     *
     * @param ResetToken $token
     * @param \DateTimeImmutable $date
     */
    public function requestPasswordReset(ResetToken $token, \DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if (!$this->email) {
            throw new \DomainException('Email is not specified.');
        }
        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Resetting is already request.');
        }
        $this->resetToken = $token;
    }
    /**
     * Запрос на смену пароля
     *
     * @param \DateTimeImmutable $date
     * @param string $hash
     */
    public function passwordReset(\DateTimeImmutable $date, string $hash): void
    {
        if (!$this->resetToken) {
            throw new \DomainException('Resisting is not requested.');
        }
        if ($this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Reset token is expired.');
        }
        $this->passwordHash = $hash;
        $this->resetToken = null;
    }
    /**
     * Смена роли
     *
     * @param Role $role
     */
    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)) {
            throw new \DomainException('Role is already same.');
        }
        $this->role = $role;
    }
    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }
    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    /**
     * @return bool
     */
    public function isWait()
    {
        return $this->status === self::STATUS_WAIT;
    }
    /**
     * Метод для установки статуса пользователя в
     * ожидание подтверждения регистрации
     */
    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }
    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }
    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    /**
     * @return Email|null
     */
    public function getEmail(): ?Email
    {
        return $this->email;
    }
    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
    /**
     * @return string | null
     */
    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }
    /**
     * @return ResetToken | null
     */
    public function getRequestToken(): ?ResetToken
    {
        return $this->resetToken;
    }
    /**
     * Событие жизенного цикла доктрины
     * Срабатывает каждый раз после мапинга сущности из базы
     *
     * Метода сбрасывает значение resetToken в null при
     * мапенге. Поведение по умолчанию у доктрины
     * ResetToken::token = null
     * ResetToken::expires = null
     * (Меняем дефолтное поведение)
     *
     */
//    public function CheckEmbeds()
//    {
//        if ($this->resetToken->isEmpty()) {
//            $this->resetToken = null;
//        }
//    }
    /**
     * @return Network[]|ArrayCollection
     */
    public function getNetworks()
    {
        return $this->networks->toArray();
    }

    /**
     * @param string $network
     * @param string $identity
     */
    private function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isNetworkAttached($network)) {
                throw new \DomainException('Network is already attached.');
            }
        }

        $this->networks->add(new Network($this, $network, $identity));
    }
}