<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Service;

use App\Domain\Auth\User\Entity\User\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class SignUpConfirmTokenSender
{
    private MailerInterface $mailer;
    private Environment $twig;

    /**
     * ConfirmTokenSender constructor.
     * @param MailerInterface $mailer
     * @param Environment $twig
     */
    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param Email $email
     * @param string $token
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function send(Email $email, string $token): void
    {
        $message = (new TemplatedEmail())
            ->from('mail@app.test')
            ->to($email->getValue())
            ->html($this->twig->render('mail/user/signup.html.twig', [
                'token' => $token
            ]));

        $this->mailer->send($message);
    }
}