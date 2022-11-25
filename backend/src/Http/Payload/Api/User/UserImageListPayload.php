<?php

declare(strict_types=1);

namespace App\Http\Payload\Api\User;

use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserImageListPayload implements BasePayloadInterface
{
    /**
     * @Assert\NotBlank
     */
    public string $uuid;

    public bool $onlyInactive = false;

    public ?int $page = null;

    public ?int $perPage = null;

    public ?string $sort = null;

    public ?string $direction = null;
}
