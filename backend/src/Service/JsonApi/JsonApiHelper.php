<?php

declare(strict_types=1);

namespace App\Service\JsonApi;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class JsonApiHelper
{
    private SerializerInterface $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createJsonResponse(
        ResponseDataBuilder $responseDataBuilder,
        int                 $status = Response::HTTP_OK,
        array               $headers = [],
        array               $context = []
    ): JsonResponse
    {
        $json = $this->serializer->serialize($responseDataBuilder->toArray(), 'json', $context);
        return new JsonResponse($json, $status, $headers, true);
    }

    public function createJsonResponseFromError(
        \Throwable $error,
        int $status = Response::HTTP_UNPROCESSABLE_ENTITY
    ): JsonResponse
    {
        $responseDataBuilder = new ResponseDataBuilder();
        $responseDataBuilder->setErrorDetail($error->getMessage());

        return $this->createJsonResponse(
            $responseDataBuilder,
            $status
        );
    }
}