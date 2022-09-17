<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Activate;

class Command
{
    /**
     * @Assert\NotBlank
     */
    public string $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }
}