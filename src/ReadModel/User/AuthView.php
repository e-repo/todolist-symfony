<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;

class AuthView
{
    public ?Id $id;
    public ?Email $email;
    public ?string $passwordHash;
    public ?Role $role;
    public string $status;

    public static function fromArray(array $params): self
    {
        $authView = new self();

        $authView->id               = $params['id'] ?? null;
        $authView->email            = $params['email'] ?? null;
        $authView->passwordHash     = $params['passwordHash'] ?? null;
        $authView->role             = $params['role'] ?? null;
        $authView->status           = $params['status'] ?? '';

        return $authView;
    }
}