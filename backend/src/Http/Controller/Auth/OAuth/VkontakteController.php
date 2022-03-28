<?php

declare(strict_types=1);

namespace App\Http\Controller\Auth\OAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VkontakteController extends AbstractController
{
    /**
     * @Route("/oauth/vkontakte", name="oauth.vkontakte")
     * @param ClientRegistry $clientRegistry
     * @return Response
     */
    public function connect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('vkontakte_main')
            ->redirect(['public_profile']);
    }

    /**
     * @Route("/oauth/vkontakte/check", name="oauth.vkontakte_check")
     * @return Response
     */
    public function check(): Response
    {
        return $this->redirectToRoute('home');
    }
}