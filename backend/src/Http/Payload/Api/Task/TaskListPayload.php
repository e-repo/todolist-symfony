<?php

declare(strict_types=1);

namespace App\Http\Payload\Api\Task;

use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TaskListPayload implements BasePayloadInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    public string $userUuid;
    /**
     * @Assert\NotBlank()
     * @Assert\Choice({"published","fulfilled"})
     */
    public string $status;
    /**
     * @Assert\DateTime("U")
     */
    public ?int $publishedStartDate = null;
    /**
     * @Assert\DateTime("U")
     */
    public ?int $publishedEndDate = null;
    public ?int $page = null;
    public ?int $perPage = null;
    public ?string $sort = null;
    public ?string $direction = null;
}
