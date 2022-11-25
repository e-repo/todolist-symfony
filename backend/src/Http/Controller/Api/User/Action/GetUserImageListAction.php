<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\User\Action;

use App\Domain\Auth\User\Read\Filter\ImageListFilter;
use App\Domain\Auth\User\Read\ImageFetcher;
use App\Http\Controller\Api\User\Response\ImageResponseDto;
use App\Http\Payload\Api\User\UserImageListPayload;
use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use App\Http\Service\BaseActionInterface;
use App\Http\Service\JsonApi\JsonApiHelper;
use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
use App\Infrastructure\Upload\UploadHelper;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GetUserImageListAction implements BaseActionInterface
{
    private const DEFAULT_PER_PAGE = 8;
    private ImageFetcher $imageFetcher;
    private UrlGeneratorInterface $urlGenerator;
    private JsonApiHelper $apiHelper;
    private CacheManager $imagineCacheManager;
    private UploadHelper $uploadHelper;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param ImageFetcher $imageFetcher
     * @param UploadHelper $uploadHelper
     * @param JsonApiHelper $apiHelper
     * @param CacheManager $imagineCacheManager
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ImageFetcher $imageFetcher,
        UploadHelper $uploadHelper,
        JsonApiHelper $apiHelper,
        CacheManager $imagineCacheManager
    )
    {
        $this->imageFetcher = $imageFetcher;
        $this->urlGenerator = $urlGenerator;
        $this->apiHelper = $apiHelper;
        $this->imagineCacheManager = $imagineCacheManager;
        $this->uploadHelper = $uploadHelper;
    }

    /**
     * @param BasePayloadInterface|UserImageListPayload $payload
     * @return JsonResponse
     */
    public function handle(BasePayloadInterface $payload): JsonResponse
    {
        $filter = (new ImageListFilter())
            ->setUuid($payload->uuid)
            ->setOnlyInactive($payload->onlyInactive);

        $pagination = $this->imageFetcher->allByFilter(
            $filter,
            $payload->page ?? 1,
            $payload->perPage ?? self::DEFAULT_PER_PAGE,
            $payload->sort ?? 'created_at',
            $payload->direction ?? 'desc'
        );

        $pagination->setItems($this->wrapPaginationItems($pagination->getItems()));

        $total = $pagination->getTotalItemCount();
        $numberOfPages = (int)\ceil($total/self::DEFAULT_PER_PAGE);

        $linkSelf = $this->urlGenerator->generate(
            'user_image.list',
            ['page' => $pagination->getCurrentPageNumber()]
        );

        $linkFirst = $this->urlGenerator->generate(
            'user_image.list',
            ['page' => 1]
        );

        $responseDataBuilder = ResponseDataBuilder::create()
            ->setLinksSelf($linkSelf)
            ->setLinksFirst($linkFirst)
            ->setMetaAttribute('totalPage', $numberOfPages)
            ->setMetaAttribute('currentPage', $pagination->getCurrentPageNumber())
            ->setMetaAttribute('perPage', self::DEFAULT_PER_PAGE)
            ->setDataType('User images')
            ->setDataAllParams($pagination->getItems());

        if ($numberOfPages > 1) {
            $linkLast = $this->urlGenerator->generate(
                'user_image.list',
                ['page' => $numberOfPages]
            );

            $responseDataBuilder
                ->setLinksLast($linkLast);
        }

        if ($pagination->getCurrentPageNumber() < $numberOfPages) {
            $linkNext = $this->urlGenerator->generate(
                'user_image.list',
                ['page' => $pagination->getCurrentPageNumber() + 1]
            );

            $responseDataBuilder
                ->setLinksNext($linkNext);
        }

        if ($pagination->getCurrentPageNumber() > 1) {
            $linkPrev = $this->urlGenerator->generate(
                'user_image.list',
                ['page' => $pagination->getCurrentPageNumber() - 1]
            );

            $responseDataBuilder
                ->setLinksPrev($linkPrev);
        }

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
    }

    /**
     * @param iterable $items
     * @return ImageResponseDto[]
     */
    private function wrapPaginationItems(iterable $items): array
    {
        return array_map(
            function(array $item) {
                $filepath = $this->imagineCacheManager
                    ->getBrowserPath(
                        sprintf('%s/%s', $item['filepath'], $item['filename']),
                        'squared_thumbnail_sm',
                        [],
                        null,
                        UrlGeneratorInterface::ABSOLUTE_PATH
                    );

                return (new ImageResponseDto())
                    ->setUuid($item['uuid'])
                    ->setOriginalFilename($item['original_filename'])
                    ->setFilename($item['filename'])
                    ->setFilepath($filepath)
                    ->setIsActive($item['is_active']);

            },
            (array) $items
        );
    }
}
