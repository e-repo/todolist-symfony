<?php

declare(strict_types=1);

namespace App\Infrastructure\Email;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;

class AppTemplateCreator
{
    private string $from;

    /**
     * AppTemplateCreator constructor.
     * @param string $from
     */
    public function __construct(string $from)
    {
        $this->from = $from;
    }

    /**
     * Возвращает шаблон письма с уже
     * установленным отправителем (от приложения)
     *
     * @return Email
     */
    public function getTemplate(): Email
    {
        return (new TemplatedEmail())->from($this->from);
    }
}