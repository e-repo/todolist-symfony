<?php

declare(strict_types=1);

namespace App\Service\JsonApi\ResponseBuilder\Params;

use phpDocumentor\Reflection\Types\This;

class ErrorParams extends AbstractParams
{
    private ?string $errorDetail = null;

    private array $errorProperties = [
        'detail'
    ];

    public function getErrorDetail(): ?string
    {
        return $this->errorDetail;
    }

    public function setErrorDetail(?string $errorDetail): self
    {
        $this->errorDetail = $errorDetail;
        return $this;
    }

    public function getProperties(): array
    {
        return $this->errorProperties;
    }
}