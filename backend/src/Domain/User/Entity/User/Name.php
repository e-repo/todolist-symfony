<?php

namespace App\Domain\User\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * Class Name
 * @package App\Domain\User\Entity\User
 *
 * @ORM\Embeddable
 */
class Name
{
    /**
     * @var string|null
     * @ORM\Column(type="string")
     */
    private string $first;
    /**
     * @var string|null
     * @ORM\Column(type="string")
     */
    private string $last;

    /**
     * Name constructor.
     * @param string $first
     * @param string $last
     */
    public function __construct(string $first, string $last = '')
    {
        Assert::notEmpty($first);
        $this->first = $first;
        $this->last = $last;
    }

    public function getFirst(): string
    {
        return $this->first;
    }

    public function getLast(): string
    {
        return $this->last;
    }

    public function getFull(): string
    {
        return sprintf('%s %s', $this->first, $this->last);
    }
}