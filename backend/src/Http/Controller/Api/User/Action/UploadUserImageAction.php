<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\User\Action;

use App\Domain\Auth\User\UseCase;
use App\Domain\Auth\User\Entity\User\Id;
use App\Domain\Auth\User\Repository\ImageRepository;
use App\Http\Payload\Api\User\UserImagePayload;
use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use App\Http\Service\BaseActionInterface;
use App\Http\Service\JsonApi\JsonApiHelper;
use App\Http\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
use App\Infrastructure\Upload\UploadHelper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypesInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadUserImageAction implements BaseActionInterface
{
    private UseCase\Image\Attach\Handler $handler;
    private ImageRepository $imageRepository;
    private UploadHelper $uploadHelper;
    private ValidatorInterface $validator;
    private MimeTypesInterface $mimeTypes;
    private JsonApiHelper $apiHelper;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        UrlGeneratorInterface                              $urlGenerator,
        UseCase\Image\Attach\Handler                       $handler,
        ImageRepository                                    $imageRepository,
        UploadHelper                                       $uploadHelper,
        ValidatorInterface                                 $validator,
        MimeTypesInterface                                 $mimeTypes,
        JsonApiHelper                                      $apiHelper
    )
    {
        $this->handler = $handler;
        $this->imageRepository = $imageRepository;
        $this->uploadHelper = $uploadHelper;
        $this->validator = $validator;
        $this->mimeTypes = $mimeTypes;
        $this->apiHelper = $apiHelper;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param BasePayloadInterface|UserImagePayload $payload
     * @return JsonResponse
     */
    public function handle(BasePayloadInterface $payload): JsonResponse
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $payload->getImage();

        $violations = $this->validator->validate($uploadedFile, [
            new NotBlank(),
            new File([
                'maxSize' => '2M',
                'mimeTypes' => \array_merge($this->mimeTypes->getMimeTypes('jpeg'), $this->mimeTypes->getMimeTypes('png'))
            ])
        ]);

        if ($violations->count() > 0) {
            $responseDataBuilder = ResponseDataBuilder::create();

            /** @var ConstraintViolation $violation */
            foreach ($violations as $violation) {
                $responseDataBuilder
                    ->setErrorsTitle('Image validation error.')
                    ->setErrorsDetail($violation->getMessage());
            }

            return $this->apiHelper->createJsonResponse($responseDataBuilder, Response::HTTP_BAD_REQUEST);
        }

        $command = new UseCase\Image\Attach\Command($uploadedFile, $payload->getUuid());

        try {
            $this->handler->handle($command);
        } catch (\Exception $e) {
            $responseDataBuilder = ResponseDataBuilder::create()
                ->setErrorsTitle('Image save error.')
                ->setErrorsDetail($e->getMessage());

            return $this->apiHelper->createJsonResponse($responseDataBuilder, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $linkSelf = $this->urlGenerator->generate(
            'user_profile.change_name',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $activeImage = $this->imageRepository->findActiveImageByUserId(new Id($payload->getUuid()));

        $responseDataBuilder = ResponseDataBuilder::create()
            ->setLinksSelf($linkSelf)
            ->setDataType('Image save successfully.')
            ->setDataAttribute('imagePath', $this->uploadHelper->getPublicPath($activeImage->getFilePath()));

        return $this->apiHelper->createJsonResponse($responseDataBuilder);
    }
}