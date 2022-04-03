<?php

declare(strict_types=1);

namespace App\Http\Controller\Api\User;

use App\Http\Payload\Api\User\UserListPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/v1/user/list", name=".list", methods={"GET"})
     * @param UserListPayload $payload
     * @return JsonResponse
     */
    public function getUserList(UserListPayload $payload): JsonResponse
    {
        dd($payload);
    }
}