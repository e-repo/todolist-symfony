<?php

declare(strict_types=1);

namespace App\Http\Payload\Api\User;

use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ImageUploadPayload implements BasePayloadInterface
{
    /**
     * @Assert\NotBlank(message="Пустой идентификатор пользователя.")
     * @Assert\Uuid(message="Идетрификатор пользователя не является корректным UUID.")
     */
    public string $uuid;

    /**
     * @Assert\Length(
     *     max=50,
     *     maxMessage="Название файла не может содержать более 50-ти символов.",
     *     min=1,
     *     minMessage="Назавние файла должно быть более 1-го символа."
     * )
     * @Assert\NotBlank(message="Пустое название файла.")
     */
    public string $imageName;

    /**
     * @Assert\File(
     *     maxSize="5M",
     *     mimeTypes={"image/x-jpeg2000-image","image/jpeg","image/jpeg2000","image/jpeg2000-image","image/pjpeg","image/png",""}
     * )
     */
    public UploadedFile $image;
}