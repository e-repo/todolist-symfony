<?php

declare(strict_types=1);

namespace App\Http\Service\JsonApi\ResponseBuilder\Params;

class LinksParams extends AbstractParams
{
    private ?string $linkSelf = null;
    private ?string $linkFirst = null;
    private ?string $linkPrev = null;
    private ?string $linkNext = null;
    private ?string $linkLast = null;

    private array $linkProperties = [
        'self',
        'first',
        'prev',
        'next',
        'last'
    ];

    public function getLinksSelf(): ?string
    {
        return $this->linkSelf;
    }

    public function setLinksSelf(?string $linkSelf): void
    {
        $this->linkSelf = $linkSelf;
    }

    public function getLinksFirst(): ?string
    {
        return $this->linkFirst;
    }

    public function setLinksFirst(?string $linkFirst): void
    {
        $this->linkFirst = $linkFirst;
    }

    public function getLinksPrev(): ?string
    {
        return $this->linkPrev;
    }

    public function setLinksPrev(?string $linkPrev): void
    {
        $this->linkPrev = $linkPrev;
    }

    public function getLinksNext(): ?string
    {
        return $this->linkNext;
    }

    public function setLinksNext(?string $linkNext): void
    {
        $this->linkNext = $linkNext;
    }

    public function getLinksLast(): ?string
    {
        return $this->linkLast;
    }

    public function setLinksLast(?string $linkLast): void
    {
        $this->linkLast = $linkLast;
    }

    public function createProperties(): array
    {
        [$linksParams] = parent::createProperties();
        return $linksParams;
    }

    protected function getProperties(): array
    {
        return $this->linkProperties;
    }
}