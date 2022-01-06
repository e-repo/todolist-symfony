<?php

declare(strict_types=1);

namespace App\Model\Todos\UseCase\File\Delete;

use Symfony\Component\Validator\Constraints as Assert;


class Command
{
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $fileId;

    /**
     * Command constructor.
     * @param string $fileId
     */
    public function __construct(string $fileId)
    {
        $this->fileId = $fileId;
    }
}