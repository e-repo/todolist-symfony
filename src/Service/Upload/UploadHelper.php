<?php

declare(strict_types=1);

namespace App\Service\Upload;

use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadHelper
{
    private FilesystemInterface $filesystem;
    private LoggerInterface $logger;
    private RequestStackContext $requestStackContext;
    private string $uploadedAssetsBaseUrl;

    /**
     * UploadHelper constructor.
     * @param RequestStackContext $requestStackContext
     * @param FilesystemInterface $uploadsFilesystem
     * @param LoggerInterface $logger
     * @param string $uploadedAssetsBaseUrl
     */
    public function __construct(
        string $uploadedAssetsBaseUrl,
        RequestStackContext $requestStackContext,
        FilesystemInterface $uploadsFilesystem,
        LoggerInterface $logger
    )
    {
        $this->filesystem = $uploadsFilesystem;
        $this->logger = $logger;
        $this->requestStackContext = $requestStackContext;
        $this->uploadedAssetsBaseUrl = $uploadedAssetsBaseUrl;
    }

    /**
     * @param File $file
     * @param string|null $entityName
     * @param string|null $entityId
     * @param null $newName
     * @return string
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadFile(File $file, string $entityName = '', ?string $entityId = '', $newName = null): string
    {
        $newFilename = $newName ?? $this->getNewFileName($file);
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

    /**
     * @param string $filePath
     * @return resource
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function readStream(string $filePath)
    {
        $resource = $this->filesystem->readStream($filePath);

        if ($resource === false) {
            throw new \RuntimeException(\sprintf('Error opening stream for "%s"', $filePath));
        }

        return $resource;
    }

    /**
     * @param string $filePath
     * @return bool
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function deleteFile(string $filePath): bool
    {
        return $this->filesystem->delete($filePath);
    }

    public function getNewFileName(File $file): string
    {
        $originalFileName = $this->getOriginalFileName($file);
        return Urlizer::transliterate($originalFileName) . \uniqid() . '.' . $file->guessExtension();
    }

    public function getPublicPath(string $path): string
    {
        /**
         * requestStackContext - для случаев, если н-р сайт был развернут в подкаталоге домена
         * (служба используется для определения подкаталога)
         */
        return \sprintf('%s/%s',
            $this->requestStackContext->getBasePath() . $this->uploadedAssetsBaseUrl,
            $path
        );
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