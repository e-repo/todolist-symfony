<?php

declare(strict_types=1);

namespace App\Service\Upload;

use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadHelper
{
    private FilesystemInterface $filesystem;
    private LoggerInterface $logger;

    /**
     * UploadHelper constructor.
     * @param FilesystemInterface $filesystem
     * @param LoggerInterface $logger
     */
    public function __construct(FilesystemInterface $uploadsFilesystem, LoggerInterface $logger)
    {
        $this->filesystem = $uploadsFilesystem;
        $this->logger = $logger;
    }

    /**
     * @param File $file
     * @param string|null $entityName
     * @param string|null $entityId
     * @return string
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadFile(File $file, string $entityName = '', ?string $entityId = ''): string
    {
        $originalFileName = $this->getOriginalFileName($file);
        $newFilename = Urlizer::transliterate($originalFileName) . \uniqid() . '.' . $file->guessExtension();

        $entityPath = $this->defineEntityPath($entityName, $entityId);

        $stream = \fopen($file->getPathname(), 'r');
        $result = $this->filesystem->writeStream(
            $entityPath . DIRECTORY_SEPARATOR . $newFilename,
            $stream
        );

        if ($result === false) {
            throw new \DomainException(\sprintf('Could not write uploaded file"%s"', $newFilename));
        }

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $newFilename;
    }

    private function getOriginalFileName(File $file): string
    {
        if ($file instanceof UploadedFile) {
            return $file->getClientOriginalName();
        }

        return $file->getFilename();
    }

    private function defineEntityPath(string $entityName, string $entityId): ?string
    {
        if (\trim($entityName) !== '' && \trim($entityId) !== '') {
            return $entityName . DIRECTORY_SEPARATOR . $entityId;
        }

        if (\trim($entityName) !== '') {
            return $entityName;
        }

        return '';
    }

}