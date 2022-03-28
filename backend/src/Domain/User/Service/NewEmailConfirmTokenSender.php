<?php

declare(strict_types=1);

namespace App\Domain\User\Service;

use App\Domain\User\Entity\User\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class NewEmailConfirmTokenSender
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;
    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * NewEmailConfirmTokenSender constructor.
     * @param MailerInterface $mailer
     * @param Environment $twig
     */
    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(Email $email, string $token): void
    {
        $message = (new TemplatedEmail())
            ->from('mail@app.test')
            ->to($email->getValue())
            ->html($this->twig->render('mail/user/email.html.twig', [
                'token' => $token
            ]), 'text/html');

        if (! $this->mailer->send($message)) {
            throw new \RuntimeException('Unable to send message.');
        }
    }
}