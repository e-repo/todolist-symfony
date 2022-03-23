<?php

declare(strict_types=1);

namespace App\Service\JsonApi\ResponseBuilder;

use App\Service\JsonApi\ResponseBuilder\Params\AbstractParams;
use App\Service\JsonApi\ResponseBuilder\Params\DataParams;
use App\Service\JsonApi\ResponseBuilder\Params\ErrorParams;
use App\Service\JsonApi\ResponseBuilder\Params\LinkParams;

class ResponseDataBuilder
{
    private DataParams $dataParams;

    private LinkParams $linkParams;

    private ErrorParams $errorParams;

    public function __construct()
    {
        $this->init();
    }

    public static function create(): self
    {
        $build = new self();
        $build->init();

        return $build;
    }

    public function setLinkSelf(?string $linkSelf): self
    {
        $this->linkParams->setLinkSelf($linkSelf);
        return $this;
    }

    public function getLinkSelf(): ?string
    {
        return $this->linkParams->getLinkSelf();
    }

    public function setLinkFirst(?string $linkSelf): self
    {
        $this->linkParams->setLinkFirst($linkSelf);
        return $this;
    }

    public function getLinkFirst(): ?string
    {
        return $this->linkParams->getLinkFirst();
    }

    public function setLinkPrev(?string $linkSelf): self
    {
        $this->linkParams->setLinkPrev($linkSelf);
        return $this;
    }

    public function getLinkPrev(): ?string
    {
        return $this->linkParams->getLinkPrev();
    }

    public function setLinkNext(?string $linkSelf): self
    {
        $this->linkParams->setLinkNext($linkSelf);
        return $this;
    }

    public function getLinkNext(): ?string
    {
        return $this->linkParams->getLinkNext();
    }

    public function setLinkLast(?string $linkSelf): self
    {
        $this->linkParams->setLinkLast($linkSelf);
        return $this;
    }

    public function getLinkLast(): ?string
    {
        return $this->linkParams->getLinkLast();
    }

    public function setDataType(string $dataType): self
    {
        $this->dataParams->setDataType($dataType);
        return $this;
    }

    public function getDataType(): ?string
    {
        return $this->dataParams->getDataType();
    }

    public function setDataAttribute(string $key, $value): self
    {
        $this->dataParams->setDataAttribute($key, $value);
        return $this;
    }

    public function getDataAttributes(): array
    {
        return $this->dataParams->getDataAttributes();
    }

    public function findDataAttribute(string $key)
    {
        return $this->dataParams->findDataAttribute($key);
    }

    public function getDataId(): ?int
    {
        return $this->dataParams->getDataId();
    }

    public function setDataId(?int $dataId): self
    {
        $this->dataParams->setDataId($dataId);
        return $this;
    }

    public function getErrorDetail(): ?string
    {
        return $this->errorParams->getErrorDetail();
    }

    public function setErrorDetail(?string $errorDetail): void
    {
        $this->errorParams->setErrorDetail($errorDetail);
    }

    /**
     * @return array
     * @throws GetterNotFoundException
     */
    public function toArray(): array
    {
        $data = $this->createProperties($this->dataParams);
        $links = $this->createProperties($this->linkParams);
        $error = $this->createProperties($this->errorParams);

        $result = [];

        if ([] !== $data) {
            $result['data'][] = $data;
        }

        if ([] !== $error) {
            $result['errors'] = $error;
        }

        if ([] !== $links) {
            $result['links'] = $links;
        }

        return $result;
    }

    /**
     * @param AbstractParams $params
     * @return array
     * @throws GetterNotFoundException
     */
    private function createProperties(AbstractParams $params): array
    {

        $paramsClassName = (new \ReflectionClass($params))->getShortName();
        $partsClassName = \array_filter(
            \preg_split('/(?=[A-Z])/', $paramsClassName)
        );
        $firstPartClassName = \array_shift($partsClassName);

        $properties = [];
        foreach ($params->getProperties() as $property) {
            $getterName = \sprintf('get' . $firstPartClassName . '%s', \ucfirst($property));

            if (! \method_exists($this, $getterName)) {
                throw new GetterNotFoundException($getterName);
            }

            if ($value = $this->$getterName()) {
                $properties[$property] = $value;
            }
        }

        return $properties;
    }

    protected function init(): void
    {
        $this->linkParams = new LinkParams();
        $this->dataParams = new DataParams();
        $this->errorParams = new ErrorParams();
    }
}