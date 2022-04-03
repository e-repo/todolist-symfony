<?php

declare(strict_types=1);

namespace App\Http\Service\JsonApi\ResponseBuilder\Params;

class ErrorsParams extends AbstractParams
{
    private ?string $errorDetail = null;

    private array $errorProperties = [
        'detail'
    ];

    public function getErrorsDetail(): ?string
    {
        return $this->errorDetail;
    }

    public function setErrorsDetail(?string $errorDetail): self
    {
        $this->errorDetail = $errorDetail;
        return $this;
    }

    protected function getProperties(): array
    {
        return $this->errorProperties;
    }
}