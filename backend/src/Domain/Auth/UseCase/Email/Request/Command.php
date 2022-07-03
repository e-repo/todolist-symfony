<?php

declare(strict_types=1);

namespace App\Domain\Auth\UseCase\Email\Request;

class Command
{
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $id;
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $email;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}