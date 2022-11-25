<?php

declare(strict_types=1);

namespace App\Http\Payload\Api\User;

use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserImageActivatePayload implements BasePayloadInterface
{
    /**
     * @Assert\NotBlank
     * @Assert\Uuid
     */
    public string $userUuid;

    /**
     * @Assert\NotBlank
     * @Assert\Uuid
     */
    public string $imageUuid;
}
