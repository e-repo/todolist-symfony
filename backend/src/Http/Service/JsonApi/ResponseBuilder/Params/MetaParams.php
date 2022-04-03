<?php

declare(strict_types=1);

namespace App\Http\Service\JsonApi\ResponseBuilder\Params;

use phpDocumentor\Reflection\Types\Self_;

class MetaParams extends AbstractParams
{
    public const COMMON_META_PROPERTY = 'attributes';

    private array $metaAttributes = [];

    private array $metaProperties = [
        self::COMMON_META_PROPERTY
    ];

    public function setMetaAttribute(string $key, $value): self
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('"key" cannot be empty.');
        }

        if (isset($this->dataAttributes[$key]) && $this->dataAttributes[$key] === $value) {
            throw new \InvalidArgumentException('Meta attribute already set.');
        }

        $this->metaAttributes[$key] = $value;
        return $this;
    }

    public function getMetaAttributes(): array
    {
        return $this->metaAttributes;
    }

    public function findMetaAttribute(string $key)
    {
        return $this->metaAttributes[$key] ?? null;
    }

    public function createProperties(): array
    {
        [$metaParams] = parent::createProperties();
        return $metaParams[self::COMMON_META_PROPERTY] ?? [];
    }

    protected function getProperties(): array
    {
        return $this->metaProperties;
    }
}