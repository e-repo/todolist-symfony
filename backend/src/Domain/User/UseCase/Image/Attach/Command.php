<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase\Image\Attach;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @var UploadedFile
     */
    public UploadedFile $file;
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $userId;

    public function __construct(UploadedFile $file, string $userId)
    {
        $this->file = $file;
        $this->userId = $userId;
    }
}