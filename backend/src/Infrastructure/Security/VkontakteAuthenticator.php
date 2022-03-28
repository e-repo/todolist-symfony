<?php

namespace App\Infrastructure\Security;

use App\Domain\User\UseCase\Network\Auth\Command;
use App\Domain\User\UseCase\Network\Auth\Handler;
use J4k\OAuth2\Client\Provider\User;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
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
use function App\Security\strtr;

class VkontakteAuthenticator extends SocialAuthenticator
{
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;
    /**
     * @var ClientRegistry
     */
    private ClientRegistry $clients;
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

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    public function supports(Request $request): bool
    {
        return $request->get('_route') === 'oauth.vkontakte_check';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getVkontakteClient());
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        /* @var User $vkontakteUser */
        $vkontakteUser = $this->getVkontakteClient()->fetchUserFromToken($credentials);

        $network = 'vkontakte';
        $id = $vkontakteUser->getId();
        $username = $network . ':' . $id;

        $command = new Command($network, $id);
        $command->firstName = $vkontakteUser->getFirstName();
        $command->lastName = $vkontakteUser->getLastName();

        try {
            return $userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $e) {
            $this->handler->handle($command);
            return $userProvider->loadUserByUsername($username);
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    /**
     * @return OAuth2ClientInterface
     */
    private function getVkontakteClient(): OAuth2ClientInterface
    {
        return $this->clients->getClient('vkontakte_main');
    }
}