<?php

declare(strict_types=1);

namespace App\Http\Payload\Api\User;

use App\Http\Service\ArgumentResolver\BasePayloadInterface;

class UserListPayload implements BasePayloadInterface
{
    public ?int $id;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $role = null;
    public ?string $status = null;
}