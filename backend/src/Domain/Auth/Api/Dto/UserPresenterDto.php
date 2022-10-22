<?php

namespace App\Domain\Auth\Api\Dto;

use App\Domain\Auth\User\Entity\User\Name;

class UserPresenterDto
{
    private string $id;
    private \DateTimeImmutable $date;
    private string $role;
    private string $status;
    private ?Name $name;
    private array $networks;
    private ?string $activeImageUuid;
    private ?string $email;

    public function __construct(
        string $id,
        \DateTimeImmutable $date,
        string $role,
        string $status,
        array $networks,
        ?Name $name = null,
        ?string $activeImageUuid = null,
        ?string $email = null
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->date = $date;
        $this->role = $role;
        $this->status = $status;
        $this->networks = $networks;
        $this->activeImageUuid = $activeImageUuid;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getId(): string
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
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return Name|null
     */
    public function getName(): ?Name
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getNetworks(): array
    {
        return $this->networks;
    }

    /**
     * @return string|null
     */
    public function getActiveImageUuid(): ?string
    {
        return $this->activeImageUuid;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
}