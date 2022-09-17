<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Entity\User;

use App\Domain\Auth\User\Entity\Image\Image;
use App\Domain\Auth\User\Entity\Network\Network;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(
 *     name="user_users",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"email"}),
 *          @ORM\UniqueConstraint(columns={"reset_token"})
 *     }
 * )
 */
class User
{
    public const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    /**
     * @ORM\Column(type="user_user_id")
     * @ORM\Id()
     */
    private Id $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $date;

    /**
     * @ORM\Column(type="user_user_role")
     */
    private Role $role;

    /**
     * Доступные соцсети в отдельной таблице
     *
     * @var Network[] | ArrayCollection | null
     * @ORM\OneToMany(
     *     targetEntity="App\Domain\Auth\User\Entity\Network\Network",
     *     mappedBy="user", orphanRemoval=true, cascade={"persist"}
     * )
     */
    private $networks = null;

    /**
     * @var Image[] | ArrayCollection | null
     * @ORM\OneToMany(
     *     targetEntity="App\Domain\Auth\User\Entity\Image\Image",
     *     mappedBy="user", orphanRemoval=true, cascade={"persist"}
     * )
     */
    private $images = null;

    /**
     * @ORM\Column(type="user_user_email", nullable=true)
     */
    private ?Email $email = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $passwordHash = null;

    /**
     * Токен для подтверждения пользователя
     * через email, соцсети и тп.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $confirmToken = null;

    /**
     * @ORM\Embedded(class="Name")
     */
    private ?Name $name = null;

    /**
     * @var Email|null
     * @ORM\Column(type="user_user_email", name="new_email", nullable=true)
     */
    private ?Email $newEmail = null;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="new_email_token", nullable=true)
     */
    private ?string $newEmailToken = null;

    /**
     * Токен для сброса пароля
     *
     * @ORM\Embedded(class="ResetToken", columnPrefix="reset_")
     */
    private ?ResetToken $resetToken = null;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private string $status;

    /**
     * User constructor.
     * @param Id $id
     * @param \DateTimeImmutable $date
     * @param Name $name
     */
    private function __construct(Id $id, \DateTimeImmutable $date, Name $name)
    {
        $this->id = $id;
        $this->date = $date;
        $this->name = $name;
        $this->role = Role::user();
    }

    /**
     * Регистрация с потверждением через Email
     * при помощи токена
     *
     * @param Id $id
     * @param \DateTimeImmutable $date
     * @param Name $name
     * @param Email $email
     * @param string $hash
     * @param string $token
     * @return static
     */
    public static function signUpByEmail(
        Id $id,
        \DateTimeImmutable $date,
        Name $name,
        Email $email,
        string $hash,
        string $token
    ): self
    {
        $user = new self($id, $date, $name);
        $user->email = $email;
        $user->passwordHash = $hash;
        $user->confirmToken = $token;
        $user->status = self::STATUS_WAIT;
        return $user;
    }

    /**
     * Регистрация с потверждением через Email
     * при помощи токена
     *
     * @param Id $id
     * @param \DateTimeImmutable $date
     * @param Name $name
     * @param Email $email
     * @param string $hash
     * @return static
     */
    public static function signUpByManual(
        Id $id,
        \DateTimeImmutable $date,
        Name $name,
        Email $email,
        string $hash
    ): self
    {
        $user = new self($id, $date, $name);
        $user->email = $email;
        $user->passwordHash = $hash;
        $user->status = self::STATUS_ACTIVE;
        return $user;
    }

    /**
     * Регистрация через соцсети
     *
     * @param Id $id
     * @param \DateTimeImmutable $date
     * @param Name $name
     * @param string $network
     * @param string $identity
     * @return $this
     */
    public static function signUpByNetwork(
        Id $id,
        \DateTimeImmutable $date,
        Name $name,
        string $network,
        string $identity
    ): self
    {
        $user = new self($id, $date, $name);
        $user->networks = new ArrayCollection();
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
     * Изменение email пользователя с верификацией email
     *
     * @param Email $email
     * @param string $token
     */
    public function requestEmailChanging(Email $email, string $token): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if ($this->email && $this->email->isEqual($email)) {
            throw new \DomainException('Email is already same.');
        }
        $this->newEmail = $email;
        $this->newEmailToken = $token;
    }
    /**
     * Изменение пароля пользователя после верификации
     *
     * @param string $token
     */
    public function confirmEmailChanging(string $token): void
    {
        if (!$this->newEmailToken) {
            throw new \DomainException('Changing is not requested.');
        }
        if ($this->newEmailToken !== $token) {
            throw new \DomainException('Incorrect changing token.');
        }
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }
    /**
     * Запрос на смену пароля
     *
     * @param \DateTimeImmutable $date
     * @param string $hash
     */
    public function passwordReset(\DateTimeImmutable $date, string $hash): void
    {
        if (! $this->resetToken) {
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
    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
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
    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }
    /**
     * Метод для установки статуса пользователя "Активен"
     */
    public function confirmSignUp(): void
    {
        if (! $this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }
    /**
     * Блокирование пользователя
     */
    public function block(): void
    {
        if ($this->isBlocked()) {
            throw new \DomainException('User is already blocked.');
        }

        $this->status = self::STATUS_BLOCKED;
    }
    /**
     * Активирование пользователя
     */
    public function activate(): void
    {
        if ($this->isActive()) {
            throw new \DomainException('User is already active.');
        }

        $this->status = self::STATUS_ACTIVE;
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
    public function getDate(): \DateTimeImmutable
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
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }
    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
    /**
     * Возвращает токен для подтверждения пароля
     *
     * @return string | null
     */
    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }
    /**
     * Новый email пользователя при изменении
     * @return Email|null
     */
    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }
    /**
     * Токен для изменения пароля пользователя
     * @return string|null
     */
    public function getNewEmailToken(): ?string
    {
        return $this->newEmailToken;
    }
    /**
     * Возвращает токен для сброса пароля
     *
     * @return ResetToken | null
     */
    public function getRequestToken(): ?ResetToken
    {
        return $this->resetToken;
    }
    /**
     * @return Name|null
     */
    public function getName(): ?Name
    {
        return $this->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public static function allStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_BLOCKED,
            self::STATUS_WAIT
        ];
    }

    /**
     * @param Name $name
     */
    public function changeName(Name $name): void
    {
        $this->name = $name;
    }
    /**
     * @param Email $email
     * @param Name $name
     */
    public function edit(Email $email, Name $name): void
    {
        $this->email = $email;
        $this->name = $name;
    }
    /**
     * Событие жизненного цикла доктрины
     * Срабатывает каждый раз после мапинга сущности из базы
     *
     * Метод сбрасывает значение resetToken при мапинге.
     * Поведение по умолчанию у доктрины на примере ResetToken:
     * ResetToken::token = null
     * ResetToken::expires = null
     * (Меняем дефолтное поведение)
     *
     * @ORM\PostLoad()
     */
    public function CheckEmbeds()
    {
        if ($this->resetToken->isEmpty()) {
            $this->resetToken = null;
        }
    }
    /**
     * @return array
     */
    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    /**
     * @param string $network
     * @param string $identity
     */
    public function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isNetworkAttached($network)) {
                throw new \DomainException('Network is already attached.');
            }
        }

        $this->networks->add(new Network($this, $network, $identity));
    }

    /**
     * @return Image[]|ArrayCollection|null
     */
    public function getImages(): ?Collection
    {
        return $this->images;
    }

    public function getActiveImage(): ?Image
    {
        foreach ($this->getImages() as $image) {
            if ($image->isActive()) {
                return $image;
            }
        }

        return null;
    }

    public function attachImage(Image $image): void
    {
        if (! $this->images instanceof Collection) {
            $this->images = new ArrayCollection();
        }

        foreach ($this->images as $existing) {
            if ($existing->isImageAttached($image->getFilename())) {
                throw new \DomainException('Image is already attached.');
            }

            $existing->setInactive();
        }

        $this->images->add($image);
    }
}