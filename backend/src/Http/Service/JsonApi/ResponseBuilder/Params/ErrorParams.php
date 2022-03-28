<?php

declare(strict_types=1);

namespace App\Http\Service\JsonApi\ResponseBuilder\Params;

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