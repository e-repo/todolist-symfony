<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Model\User\UseCase\Network;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VkontakteController extends AbstractController
{
    /**
     * @Route("oauth/attach/vkontacte", "oauth.attach.vkontakte")
     * @param ClientRegistry $clientRegistry
     * @return Response
     */
    public function attach(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('vkontakte_attach')
            ->redirect(['public_profile']);
    }

    /**
     * @param ClientRegistry $clientRegistry
     * @param Network\Attach\Handler $handler
     * @return Response
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function check(
        ClientRegistry $clientRegistry,
        Network\Attach\Handler $handler
    ): Response
    {
        $vkontakteUser = $clientRegistry
            ->getClient('vkontakte_attach')
            ->fetchUser();

        $command = new Network\Attach\Command(
            $this->getUser()->getId(),
            'vkontakte',
            $vkontakteUser->getId()
        );


    }
}