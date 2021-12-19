<?php

declare(strict_types=1);

namespace App\Widget\Upload;

use App\Service\Upload\UploadHelper;
use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UploadedAssetWidget extends AbstractExtension
{
    private UploadHelper $uploadHelper;

    /**
     * UploadedAssetWidget constructor.
     * @param UploadHelper $uploadHelper
     */
    public function __construct(UploadHelper $uploadHelper)
    {

        $this->uploadHelper = $uploadHelper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploaded_asset', [$this, 'uploadedAssetPath']),
        ];
    }

    public function uploadedAssetPath(string $path): string
    {
        return $this->uploadHelper->getPublicPath($path);
    }
}