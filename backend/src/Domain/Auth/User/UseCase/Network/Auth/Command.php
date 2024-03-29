<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Network\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $network;
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $identity;
    /**
     * @var string|null
     */
    public ?string $firstName;
    /**
     * @var string|null
     */
    public ?string $lastName;

    /**
     * Command constructor.
     * @param string $network
     * @param string $identity
     */
    public function __construct(string $network, string $identity)
    {
        $this->network = $network;
        $this->identity = $identity;
    }
}