<?php


namespace App\Security;


use App\Model\User\UseCase\Network\Auth\Command;
use App\Model\User\UseCase\Network\Auth\Handler;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\VKontakteClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class VkontakteAuthenticator extends SocialAuthenticator
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var ClientRegistry
     */
    private $clients;
    /**
     * @var Handler
     */
    private Handler $handler;

    /**
     * VkontakteAuthenticator constructor.
     * @param UrlGeneratorInterface $urlGenerator
     * @param ClientRegistry $clients
     * @param Handler $handler
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ClientRegistry $clients,
        Handler $handler
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->clients = $clients;
        $this->handler = $handler;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    public function supports(Request $request)
    {
        return $request->get('_route') === 'oauth.vkontakte_check';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getVkontakteClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $vkontakteUser = $this->getVkontakteClient()->fetchUserFromToken($credentials);

        $network = 'vkontakte';
        $id = $vkontakteUser->getId();
        $username = $network . ':' . $id;

        try {
            return $userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $e) {
            $this->handler->handle(new Command($network, $id));
            return $userProvider->loadUserByUsername($username);
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    /**
     * @return VKontakteClient
     */
    private function getVkontakteClient(): VKontakteClient
    {
        return $this->clients->getClient('vkontakte_main');
    }
}