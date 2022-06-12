<?php

declare(strict_types=1);

namespace App\Http\Service\JsonApi\ResponseBuilder;

use App\Http\Service\JsonApi\ResponseBuilder\Params\DataParams;
use App\Http\Service\JsonApi\ResponseBuilder\Params\ErrorsParams;
use App\Http\Service\JsonApi\ResponseBuilder\Params\LinksParams;
use App\Http\Service\JsonApi\ResponseBuilder\Params\MetaParams;

class ResponseDataBuilder
{
    private DataParams $dataParams;

    private LinksParams $linksParams;

    private ErrorsParams $errorsParams;

    private MetaParams $metaParams;

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

    public function setLinksSelf(?string $linkSelf): self
    {
        $this->linksParams->setLinksSelf($linkSelf);
        return $this;
    }

    public function getLinksSelf(): ?string
    {
        return $this->linksParams->getLinksSelf();
    }

    public function setLinksFirst(?string $linkSelf): self
    {
        $this->linksParams->setLinksFirst($linkSelf);
        return $this;
    }

    public function getLinksFirst(): ?string
    {
        return $this->linksParams->getLinksFirst();
    }

    public function setLinksPrev(?string $linkSelf): self
    {
        $this->linksParams->setLinksPrev($linkSelf);
        return $this;
    }

    public function getLinksPrev(): ?string
    {
        return $this->linksParams->getLinksPrev();
    }

    public function setLinksNext(?string $linkSelf): self
    {
        $this->linksParams->setLinksNext($linkSelf);
        return $this;
    }

    public function getLinksNext(): ?string
    {
        return $this->linksParams->getLinksNext();
    }

    public function setLinksLast(?string $linkSelf): self
    {
        $this->linksParams->setLinksLast($linkSelf);
        return $this;
    }

    public function getLinksLast(): ?string
    {
        return $this->linksParams->getLinksLast();
    }

    public function setDataAllParams(iterable $params): self
    {
        $this->dataParams->setDataAllParams($params);
        return $this;
    }

    public function getDataAllParams(): iterable
    {
        return $this->dataParams->getDataAllParams();
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

    public function getDataUuid(): ?string
    {
        return $this->dataParams->getDataUuid();
    }

    public function setDataUuid(?string $dataUuid): self
    {
        $this->dataParams->setDataUuid($dataUuid);
        return $this;
    }

    public function setMetaAttribute(string $key, $value): self
    {
        $this->metaParams->setMetaAttribute($key, $value);
        return $this;
    }

    public function getMetaAttributes(): array
    {
        return $this->metaParams->getMetaAttributes();
    }

    public function findMetaAttribute(string $key)
    {
        return $this->metaParams->findMetaAttribute($key);
    }

    public function getErrorsDetail(): ?array
    {
        return $this->errorsParams->getErrorsDetail();
    }

    public function setErrorsDetail(?string $errorDetail): self
    {
        $this->errorsParams->setErrorsDetail($errorDetail);
        return $this;
    }

    public function getErrorsStatus(): ?array
    {
        return $this->errorsParams->getErrorsStatus();
    }

    public function setErrorsStatus(?int $errorStatus): self
    {
        $this->errorsParams->setErrorsStatus($errorStatus);
        return $this;
    }

    /**
     * @return array
     * @throws GetterNotFoundException
     */
    public function toArray(): array
    {
        $dataParams = $this->dataParams->createProperties();
        $errorsParams = $this->errorsParams->createProperties();
        $linksParams = $this->linksParams->createProperties();
        $metaParams = $this->metaParams->createProperties();

        $result = [];

        if (false === empty($dataParams[0])) {
            $result['data'] = $dataParams;
        }

        if (false === empty($errorsParams[0])) {
            $result['errors'] = $errorsParams;
        }

        if (false === empty($linksParams)) {
            $result['links'] = $linksParams;
        }

        if (false === empty($metaParams)) {
            $result['meta'] = $metaParams;
        }

        return $result;
    }

    protected function init(): void
    {
        $this->linksParams = new LinksParams();
        $this->dataParams = new DataParams();
        $this->errorsParams = new ErrorsParams();
        $this->metaParams = new MetaParams();
    }
}