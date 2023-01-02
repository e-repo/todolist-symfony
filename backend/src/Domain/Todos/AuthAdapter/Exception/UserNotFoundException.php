<?php

declare(strict_types=1);

namespace App\Domain\Todos\AuthAdapter\Exception;

class UserNotFoundException extends \DomainException
{
    private const DEFAULT_MESSAGE = 'User not found';
    /**
     * @var string|null
     */
    private ?string $userUuid;

    public function __construct(?string $message = null, ?string $userUuid = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE);
        $this->userUuid = $userUuid;
    }

    /**
     * @return string|null
     */
    public function getUserUuid(): ?string
    {
        return $this->userUuid;
    }
}
