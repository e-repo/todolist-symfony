<?php

declare(strict_types=1);

namespace App\Domain\Todos\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank
     */
    public string $userId;
    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    public string $name;

    public ?string $description = null;
}