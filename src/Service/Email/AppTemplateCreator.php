<?php

declare(strict_types=1);

namespace App\Container\Common;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;

class EmailFromAppFactory
{
    /**
     * Возвращает шаблон письми с уже
     * установленным отправителем от приложения
     *
     * @param string $from
     * @return Email
     */
    public function create(string $from): Email
    {
        return (new TemplatedEmail())->from($from);
    }
}