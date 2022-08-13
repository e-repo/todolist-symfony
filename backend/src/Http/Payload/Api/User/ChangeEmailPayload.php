<?php

declare(strict_types=1);

namespace App\Http\Payload\Api\User;

use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ChangeEmailPayload implements BasePayloadInterface
{
    /**
     * @Assert\NotBlank(message="Пустой идентификатор пользователя.")
     * @Assert\Uuid(message="Идетрификатор пользователя не является корректным UUID.")
     */
    public string $uuid;

    /**
     * @Assert\NotBlank(message="Пустой email пользователя.")
     * @Assert\Email(message="Email {{ value }} не является корректрым значением.")
     */
    public string $email;
}