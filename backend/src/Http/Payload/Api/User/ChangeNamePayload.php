<?php

declare(strict_types=1);

namespace App\Http\Payload\Api\User;

use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ChangeNamePayload implements BasePayloadInterface
{
    /**
     * @Assert\NotBlank(message="Пустой идентификатор пользователя.")
     * @Assert\Uuid(message="Идетрификатор пользователя не является корректным UUID.")
     */
    public string $uuid;

    /**
     * @Assert\Length(
     *          max=50,
     *          maxMessage="Имя пользователя не может содержать более 50-ти символов.",
     *          min=2,
     *          minMessage="Имя пользователя не может содержать менее 2-ух символов."
     *      )
     * @Assert\NotBlank(message="Пустое имя пользователя.")
     */
    public string $firstName;

    /**
     * @Assert\Length(
     *          max=50,
     *          maxMessage="Фамилия пользователя не может содержать более 50-ти символов.",
     *          min=2,
     *          minMessage="Фамилия пользователя не может содержать менее 2-ух символов."
     *     )
     * @Assert\NotBlank(message="Пустая фамилия пользователя.")
     */
    public string $lastName;
}