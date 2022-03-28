<?php

declare(strict_types=1);

namespace App\Widget\User;

use App\Infrastructure\Security\UserIdentity;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NameWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('user_name', [$this, 'name'], [
                'needs_environment' => true,
                'is_safe' => ['html']
            ])
        ];
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function name(Environment $twig, UserIdentity $user): string
    {
        return $twig->render('widget/user/name.html.twig', compact('user'));
    }
}