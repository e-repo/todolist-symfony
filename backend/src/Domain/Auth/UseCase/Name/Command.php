<?php

declare(strict_types=1);

namespace App\Domain\Auth\UseCase\Name;

class Command
{
    /**
     * @Assert\NotBlank()
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
}