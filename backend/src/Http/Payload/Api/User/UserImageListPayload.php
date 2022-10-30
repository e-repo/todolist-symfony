<?php

declare(strict_types=1);

namespace App\Http\Payload\Api\User;

use App\Http\Service\ArgumentResolver\BasePayloadInterface;

class UserImageListPayload implements BasePayloadInterface
{
    public string $uuid;
    public ?int $page = null;
    public ?int $perPage = null;
    public ?string $sort = null;
    public ?string $direction = null;
}