<?php

namespace App\Http\Service;

use App\Http\Service\ArgumentResolver\BasePayloadInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

interface BaseActionInterface
{
    public function handle(BasePayloadInterface $payload): JsonResponse;
}