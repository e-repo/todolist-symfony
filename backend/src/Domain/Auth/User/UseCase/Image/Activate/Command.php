<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Image\Activate;

class Command
{
    public string $userUuid;
    public string $imageUuid;

    public function __construct(string $userUuid, string $imageUuid)
    {
        $this->userUuid = $userUuid;
        $this->imageUuid = $imageUuid;
    }
}
