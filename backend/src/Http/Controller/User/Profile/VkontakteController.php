<?php

declare(strict_types=1);

namespace App\Http\Controller\User\Profile;

use App\Domain\Auth\UseCase\Network;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class VkontakteController extends AbstractController
{
    private LoggerInterface $logger;
    private TranslatorInterface $translator;

    /**
     * VkontakteController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(
        LoggerInterface $logger,
        TranslatorInterface $translator
    )
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @Route("oauth/attach/vkontakte", name="oauth.attach.vkontakte")
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
     * @Route("oath/attach/check", name="oauth.attach.vkontakte_check")
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
            (string) $vkontakteUser->getId()
        );

        try {
            $handler->handle($command);
            $this->addFlash('success', $this->translator->trans('VK is successfully attached.', [], 'profile'));
            return $this->redirectToRoute('profile');
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('profile');
        }
    }
}