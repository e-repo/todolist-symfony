<?php

declare(strict_types=1);

namespace App\Model\Todos\Entity\Task;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Image
 * @package App\Model\Todos\Entity\Task
 * @ORM\Entity()
 * @ORM\Table(
 *     name="todos_task_file",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"filename"})}
 * )
 */
class File
{
    /**
     * @var string
     * @ORM\Column(type="guid")
     * @ORM\Id()
     * @Groups({"show_files"})
     */
    private string $id;
    /**
     * @var Task
     * @ORM\ManyToOne(targetEntity="Task", inversedBy="files")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Task $task;
    /**
     * @var string
     * @ORM\Column(type="string", length=150, nullable=false)
     * @Groups({"show_files"})
     */
    private string $filename;
    /**
     * @var string
     * @ORM\Column(type="string", length=150, nullable=false)
     * @Groups({"show_files"})
     */
    private string $originalFilename;
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $filepath;
    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     * @Groups({"show_files"})
     */
    private bool $isActive;
    /**
     * @var string
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Groups({"show_files"})
     */
    private string $mimeType;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", length=150, nullable=false)
     * @Groups({"show_files"})
     */
    private \DateTimeImmutable $createdAt;

    public function __construct(string $filename, UploadedFile $uploadedFile, Task $task)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->filename = $filename;
        $this->task = $task;
        $this->originalFilename = $uploadedFile->getClientOriginalName();
        $this->mimeType = $uploadedFile->getMimeType();
        $this->createdAt = new \DateTimeImmutable();
        $this->filepath = $this->getFileDirectory();
        $this->setActive();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Task
     */
    public function getUser(): Task
    {
        return $this->task;
    }

    /**
     * @param Task $task
     */
    public function setUser(Task $task): void
    {
        $this->task = $task;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    /**
     * @param string $originalFilename
     */
    public function setOriginalFilename(string $originalFilename): void
    {
        $this->originalFilename = $originalFilename;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType(string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable $createdAt
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    /**
     * @param string $filename
     * @return bool
     */
    public function isFileAttached(string $filename): bool
    {
        return $this->filename === $filename;
    }

    /**
     * @return string
     */
    public function getFileDirectory(): string
    {
        if (isset($this->filepath)) {
            return $this->filepath;
        }

        if ($this->task) {
            return (new \ReflectionClass($this->task))->getShortName() . DIRECTORY_SEPARATOR . $this->task->getId()->getValue();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        if ($fileDirectory = $this->getFileDirectory()) {
            return $fileDirectory . DIRECTORY_SEPARATOR . $this->filename;
        }

        return '';
    }

    /**
     * @param string $filepath
     */
    public function setFilepath(string $filepath): void
    {
        $this->filepath = $filepath;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }


    public function setActive(): void
    {
        $this->isActive = true;
    }

    public function setInactive(): void
    {
        $this->isActive = false;
    }
}