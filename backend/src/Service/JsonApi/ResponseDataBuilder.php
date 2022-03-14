<?php

declare(strict_types=1);

namespace App\Service\JsonApi;

class ResponseDataBuilder
{
    private ?string $dataType = null;

    private ?int $dataId = null;

    private array $dataAttributes = [];

    private ?string $errorDetail = null;

    private ?string $linkSelf = null;

    private array $dataProperties = ['type', 'id', 'attributes'];

    private array $errorProperties = ['detail'];

    public static function create(): self
    {
        return new self();
    }

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

    public function getLinkSelf(): ?string
    {
        return $this->linkSelf;
    }

    public function setLinkSelf(?string $linkSelf): self
    {
        $this->linkSelf = $linkSelf;
        return $this;
    }

    public function toArray(): array
    {
        $data = $this->createPropertiesData($this->dataProperties);
        $error = $this->createPropertiesData($this->errorProperties);

        $result = [];

        if (! empty($data)) {
            $result['data'] = $data;
        }

        if (! empty($error)) {
            $result['errors'] = $error;
        }

        if (null !== $this->linkSelf) {
            $result['links'] = $this->linkSelf;
        }

        return $result;
    }

    private function createPropertiesData(array $properties): array
    {
        $data = [];
        foreach ($properties as $property) {
            $propertyName = \sprintf('data%s', \ucfirst($property));

            if (! empty($this->$propertyName)) {
                $data[$property] = $this->$propertyName;
            }
        }

        return $data;
    }

    public function getErrorDetail(): ?string
    {
        return $this->errorDetail;
    }

    public function setErrorDetail(?string $errorDetail): void
    {
        $this->errorDetail = $errorDetail;
    }
}