<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Auth;


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