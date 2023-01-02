<?php

declare(strict_types=1);

namespace App\Domain\Todos\Task\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank
     */
    public string $userId;
    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    public string $name;

    public ?string $description = null;

    public function setUserUuid(string $userId): Command
    {
        $this->userId = $userId;
        return $this;
    }

    public function setName(string $name): Command
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription(?string $description): Command
    {
        $this->description = $description;
        return $this;
    }
}
