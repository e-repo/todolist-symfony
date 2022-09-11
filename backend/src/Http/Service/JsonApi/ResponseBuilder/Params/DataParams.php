<?php

declare(strict_types=1);

namespace App\Http\Service\JsonApi\ResponseBuilder\Params;

class DataParams extends AbstractParams
{
    private ?int $dataId = null;
    private ?string $dataUuid = null;
    private ?string $dataType = null;
    private iterable $dataParams = [];
    private array $dataAttributes = [];
    private iterable $dataRelationships = [];

    private array $dataProperties = [
        'allParams',
        'type',
        'id',
        'uuid',
        'attributes',
        'relationships'
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

    public function setDataAllParams(iterable $params): self
    {
        $this->dataParams = $params;
        return $this;
    }

    public function getDataAllParams(): iterable
    {
        return $this->dataParams;
    }

    public function getDataId(): ?int
    {
        return $this->dataId;
    }

    public function setDataId(int $dataId): self
    {
        $this->dataId = $dataId;
        return $this;
    }

    public function getDataUuid(): ?string
    {
        return $this->dataUuid;
    }

    public function setDataUuid(string $dataUuid): self
    {
        $this->dataUuid = $dataUuid;
        return $this;
    }

    protected function getProperties(): array
    {
        return $this->dataProperties;
    }

    public function setDataRelationships(iterable $params): self
    {
        $this->dataRelationships = $params;
        return $this;
    }

    public function getDataRelationships(): iterable
    {
        return $this->dataRelationships;
    }
}