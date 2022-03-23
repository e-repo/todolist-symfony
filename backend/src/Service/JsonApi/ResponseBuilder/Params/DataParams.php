<?php

declare(strict_types=1);

namespace App\Service\JsonApi\ResponseBuilder\Params;

class DataParams extends AbstractParams
{
    private ?int $dataId = null;

    private array $dataAttributes = [];

    private ?string $dataType = null;

    private array $dataProperties = [
        'type',
        'id',
        'attributes'
    ];

    public function setDataType(string $dataType): self
    {
        $this->dataType = $dataType;
        return $this;
    }

    public function getDataType(): ?string
    {
        return $this->dataType;
    }

    public function setDataAttribute(string $key, $value): self
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('"key" cannot be empty.');
        }

        if (isset($this->dataAttributes[$key]) && $this->dataAttributes[$key] === $value) {
            throw new \InvalidArgumentException('Data attribute already set.');
        }

        $this->dataAttributes[$key] = $value;
        return $this;
    }

    public function getDataAttributes(): array
    {
        return $this->dataAttributes;
    }

    public function findDataAttribute(string $key)
    {
        return $this->dataAttributes[$key] ?? null;
    }

    public function getDataId(): ?int
    {
        return $this->dataId;
    }

    public function setDataId(?int $dataId): self
    {
        $this->dataId = $dataId;
        return $this;
    }

    public function getProperties(): array
    {
        return $this->dataProperties;
    }
}