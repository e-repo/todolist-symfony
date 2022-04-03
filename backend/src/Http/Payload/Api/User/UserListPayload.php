<?php

declare(strict_types=1);

namespace App\Http\Payload\Api\User;

use App\Http\Service\ArgumentResolver\BasePayloadInterface;

class UserListPayload implements BasePayloadInterface
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $role = null;
    public ?string $status = null;
    public ?int $page = null;
    public ?int $offset = null;
    public ?string $sort = null;
    public ?string $direction = null;
}