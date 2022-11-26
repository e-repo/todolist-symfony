<?php

declare(strict_types=1);

namespace App\Http\Payload\Api\User;

use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class UserImagePayload implements BasePayloadInterface
{
    private string $userUuid;
    private UploadedFile $image;

    public function __construct(
        string $uuid,
        UploadedFile $image
    )
    {
        $this->userUuid = $uuid;
        $this->image = $image;
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    public function getImage(): UploadedFile
    {
        return $this->image;
    }
}
