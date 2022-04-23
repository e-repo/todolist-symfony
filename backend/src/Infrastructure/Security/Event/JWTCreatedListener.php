<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Event;

use App\Infrastructure\Security\UserIdentity;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $payloadUser['email'] = $payload['username'];
        $payloadUser['roles'] = $payload['roles'];

        unset($payload['roles']);

        /** @var UserIdentity $user */
        $user = $event->getUser();

        if (null !== $user->getName()) {
            $payloadUser['first'] = $user->getName()->getFirst();
            $payloadUser['last'] = $user->getName()->getLast();
            $payloadUser['id'] = $user->getId();
        }

        $payload['user'] = $payloadUser;
        $event->setData($payload);
    }
}