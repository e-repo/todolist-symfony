<?php

namespace App\Domain\Todos\AuthAdapter\Dto;

class UserDto
{
    private string $id;
    private \DateTimeImmutable $date;
    private string $role;
    private string $status;
    private array $networks;
    private ?string $firstname;
    private ?string $lastname;
    private ?string $activeImageUuid;
    private ?string $email;

    public function __construct(
        string $id,
        \DateTimeImmutable $date,
        string $role,
        string $status,
        array $networks,
        ?string $firstname = null,
        ?string $lastname = null,
        ?string $activeImageUuid = null,
        ?string $email = null
    )
    {
        $this->id = $id;
        $this->date = $date;
        $this->role = $role;
        $this->status = $status;
        $this->networks = $networks;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
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

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }
}