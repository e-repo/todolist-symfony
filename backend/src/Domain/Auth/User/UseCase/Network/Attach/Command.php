<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Network\Attach;

class Command
{
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $uuid;
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $network;
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $networkIdentity;

    /**
     * Command constructor.
     * @param string $uuid
     * @param string $network
     * @param string $networkIdentity
     */
    public function __construct(
        string $uuid,
        string $network,
        string $networkIdentity
    )
    {
        $this->uuid = $uuid;
        $this->network = $network;
        $this->networkIdentity = $networkIdentity;
    }
}