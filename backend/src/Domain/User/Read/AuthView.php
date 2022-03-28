<?php

declare(strict_types=1);

namespace App\Domain\User\Read;

use App\Domain\User\Entity\User\Email;
use App\Domain\User\Entity\User\Id;
use App\Domain\User\Entity\User\Role;

class AuthView
{
    public ?Id $id;
    public ?Email $email;
    public ?string $firstName;
    public ?string $lastName;
    public ?string $passwordHash;
    public ?Role $role;
    public string $status;

    public static function fromArray(array $params): self
    {
        $authView = new self();

        $authView->id               = $params['id'] ?? null;
        $authView->email            = $params['email'] ?? null;
        $authView->firstName        = $params['firstName'] ?? null;
        $authView->lastName         = $params['lastName'] ?? null;
        $authView->passwordHash     = $params['passwordHash'] ?? null;
        $authView->role             = $params['role'] ?? null;
        $authView->status           = $params['status'] ?? '';

        return $authView;
    }
}