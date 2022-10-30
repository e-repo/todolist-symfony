<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\User\Action;

use App\Http\Payload\Api\User\UserImageListPayload;
use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use App\Http\Service\BaseActionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetUserImageListAction implements BaseActionInterface
{

    /**
     * @param BasePayloadInterface|UserImageListPayload $payload
     * @return JsonResponse
     */
    public function handle(BasePayloadInterface $payload): JsonResponse
    {
        dd($payload);
    }
}