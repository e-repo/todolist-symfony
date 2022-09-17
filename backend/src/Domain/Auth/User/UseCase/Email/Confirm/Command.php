<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Email\Confirm;

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
    public string $token;

    /**
     * Command constructor.
     * @param string $id
     * @param string $token
     */
    public function __construct(string $id, string $token)
    {
        $this->id = $id;
        $this->token = $token;
    }
}