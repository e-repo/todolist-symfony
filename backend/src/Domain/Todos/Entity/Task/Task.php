<?php

declare(strict_types=1);

namespace App\Domain\Todos\Entity\Task;

use App\Domain\Auth\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Webmozart\Assert\Assert;

/**
 * Class Task
 * @package App\Domain\Todos\Entity\Task
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
     * @ORM\ManyToOne(targetEntity="App\Domain\User\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private User $user;
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $name = '';
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private string $description = '';
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private string $status;
    /**
     * @var File[] | Collection | null
     * @ORM\OneToMany(targetEntity="File", mappedBy="task", orphanRemoval=true, cascade={"persist"})
     */
    private $files = null;
    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $deletedAt = null;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $date;

    public function __construct(Id $id, User $user, Content $content, \DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->user = $user;
        $this->setName($content->getName());
        $this->setDescription($content->getDescription());
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

    public function getContent(): Content
    {
        return new Content($this->getName(), $this->getDescription());
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function delete(): void
    {
        if ($this->isDeleted()) {
            throw new \DomainException('Task is already delete.');
        }

        $this->deletedAt = new \DateTimeImmutable();
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function changeContent(Content $content): void
    {
        $this->changeName($content->getName());
        $this->changeDescription($content->getDescription());
    }

    public function setName(string $name): void
    {
        if ($this->name === \trim($name)) {
            throw new \DomainException('Task name is set.');
        }

        $this->name = $name;
    }

    public function setDescription(string $description): void
    {
        if ($this->description === \trim($description)) {
            throw new \DomainException('Task description is set.');
        }

        $this->description = $description;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isFulfilled(): bool
    {
        return $this->status === self::STATUS_FULFILLED;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt instanceof \DateTimeImmutable;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
    }

    public function changeDescription(string $description): void
    {
        $this->description = $description;
    }

    public function changeStatus(string $status): void
    {
        Assert::oneOf($status, [
            self::STATUS_FULFILLED,
            self::STATUS_PUBLISHED,
        ]);
        $this->status = $status;
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
        if ($this->isPublished()) {
            throw new \DomainException('Task is already published.');
        }

        $this->status = self::STATUS_PUBLISHED;
    }

    public function attachFile(File $file): void
    {
        if (! $this->files instanceof Collection) {
            $this->files = new ArrayCollection();
        }

        foreach ($this->files as $existing) {
            if ($existing->isFileAttached($file->getFilename())) {
                throw new \DomainException('File is already attached.');
            }

            $existing->setInactive();
        }

        $this->files->add($file);
    }

    /**
     * @return File[]|Collection|null
     */
    public function getFiles()
    {
        return $this->files->toArray();
    }
}