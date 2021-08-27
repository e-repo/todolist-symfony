<?php

declare(strict_types=1);

namespace App\Model\Todos\Entity\Task;

use App\Model\User\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Task
 * @package App\Model\Todos\Entity\Task
 * @ORM\Entity()
 * @ORM\Table(name="todos_tasks")
 */
class Task
{
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_FULFILLED = 'fulfilled';

    /**
     * @ORM\Column(type="todos_task_id")
     * @ORM\Id()
     */
    private Id $id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private User $user;
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $name;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private string $description;
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private string $status;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $date;

    public function __construct(Id $id, User $user, Content $content, \DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->user = $user;
        $this->name = $content->getName();
        $this->description = $content->getDescription();
        $this->status = self::STATUS_PUBLISHED;
        $this->date = $date;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isFulfilled(): bool
    {
        return $this->status === self::STATUS_FULFILLED;
    }

    /**
     * Устанавливает статус "выполнено"
     */
    public function fulfilled(): void
    {
        if ($this->isFulfilled()) {
            throw new \DomainException('Task is already fulfilled.');
        }

        $this->status = self::STATUS_FULFILLED;
    }

    /**
     * Устанавливает статус "опубликовано"
     */
    public function published(): void
    {
        if ($this->isFulfilled()) {
            throw new \DomainException('Task is already published.');
        }

        $this->status = self::STATUS_PUBLISHED;
    }
}