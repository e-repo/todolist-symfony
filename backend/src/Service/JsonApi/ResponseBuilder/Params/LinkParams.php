<?php

declare(strict_types=1);

namespace App\Service\JsonApi\ResponseBuilder\Params;

class LinkParams extends AbstractParams
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

    public function getLinkSelf(): ?string
    {
        return $this->linkSelf;
    }

    public function setLinkSelf(?string $linkSelf): void
    {
        $this->linkSelf = $linkSelf;
    }

    public function getLinkFirst(): ?string
    {
        return $this->linkFirst;
    }

    public function setLinkFirst(?string $linkFirst): void
    {
        $this->linkFirst = $linkFirst;
    }

    public function getLinkPrev(): ?string
    {
        return $this->linkPrev;
    }

    public function setLinkPrev(?string $linkPrev): void
    {
        $this->linkPrev = $linkPrev;
    }

    public function getLinkNext(): ?string
    {
        return $this->linkNext;
    }

    public function setLinkNext(?string $linkNext): void
    {
        $this->linkNext = $linkNext;
    }

    public function getLinkLast(): ?string
    {
        return $this->linkLast;
    }

    public function setLinkLast(?string $linkLast): void
    {
        $this->linkLast = $linkLast;
    }

    public function getProperties(): array
    {
        return $this->linkProperties;
    }
}