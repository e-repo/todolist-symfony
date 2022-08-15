<?php

declare(strict_types=1);

namespace App\Domain\Auth\UseCase\Name;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid()
     * @var string
     */
    public string $id;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max = 50)
     * @var string
     */
    public string $first;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max = 50)
     * @var string
     */
    public string $last;

    /**
     * Command constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setFirst(string $first): self
    {
        $this->first = $first;
        return $this;
    }

    public function setLast(string $last): self
    {
        $this->last = $last;
        return $this;
    }
}