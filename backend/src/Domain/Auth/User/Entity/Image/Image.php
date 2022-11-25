<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Entity\Image;

use App\Domain\Auth\User\Entity\User\User;
use App\Domain\Auth\User\Error\Image\ImageAlreadyActiveException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Image
 * @ORM\Entity()
 * @ORM\Table(
 *     name="user_image",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"filename"})}
 * )
 */
class Image
{
    /**
     * @var string
     * @ORM\Column(type="guid")
     * @ORM\Id()
     */
    private string $id;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Domain\Auth\User\Entity\User\User", inversedBy="images")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private User $user;
    /**
     * @var string
     * @ORM\Column(type="string", length=150, nullable=false)
     */
    private string $filename;
    /**
     * @var string
     * @ORM\Column(type="string", length=150, nullable=false)
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
     */
    private bool $isActive;
    /**
     * @var string
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private string $mimeType;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", length=150, nullable=false)
     */
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $filename,
        UploadedFile $uploadedFile,
        User $user,
        bool $isActive = true
    )
    {
        $this->id = Uuid::uuid4()->toString();
        $this->filename = $filename;
        $this->user = $user;
        $this->originalFilename = $uploadedFile->getClientOriginalName();
        $this->mimeType = $uploadedFile->getMimeType();
        $this->createdAt = new \DateTimeImmutable();
        $this->filepath = $this->getFileDirectory();
        $this->isActive = $isActive;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
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
    public function isImageAttached(string $filename): bool
    {
        return $this->filename === $filename;
    }

    public function isImageBelongsToUser($userUuid): bool
    {
        return $this->getUser()->getId()->getValue() === $userUuid;
    }

    /**
     * @return string
     */
    public function getFileDirectory(): string
    {
        if (isset($this->filepath)) {
            return $this->filepath;
        }

        if ($this->user) {
            return (new \ReflectionClass($this->user))->getShortName() . DIRECTORY_SEPARATOR . $this->user->getId()->getValue();
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
        if (true === $this->isActive) {
            throw new ImageAlreadyActiveException($this->getId());
        }

        $this->isActive = true;
    }

    public function setInactive(): void
    {
        $this->isActive = false;
    }
}
