<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\UseCase\File\Attach;

use Symfony\Component\HttpFoundation\File\UploadedFile;


class Command
{
    /**
     * @Assert\NotBlank()
     * @var UploadedFile $file
     */
    public UploadedFile $file;
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $taskId;

    /**
     * Command constructor.
     * @param UploadedFile $file
     * @param string $taskId
     */
    public function __construct(UploadedFile $file, string $taskId)
    {
        $this->file = $file;
        $this->taskId = $taskId;
    }
}