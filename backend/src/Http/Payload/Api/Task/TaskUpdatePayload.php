<?php

declare(strict_types=1);

namespace App\Http\Payload\Api\Task;

use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TaskUpdatePayload  implements BasePayloadInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    public string $uuid;
    /**
     * @Assert\Length(
     *          max=100,
     *          maxMessage="Наименование задачи не может содержать более 100 символов",
     *          min=2,
     *          minMessage="Наименование задачи не может содержать менее 2-ух символов"
     *      )
     * @Assert\NotBlank(message="Пустое наименование задачи")
     */
    public string $name;
    /**
     * @Assert\Length(
     *          max=250,
     *          maxMessage="Описание задачи не может содержать более 250 символов",
     *          min=2,
     *          minMessage="Описание задачи не может содержать менее 5-ти символов"
     *      )
     * @Assert\NotBlank(message="Пустое наименование задачи")
     */
    public string $description;
}
