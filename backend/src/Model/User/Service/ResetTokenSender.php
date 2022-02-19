<?php


namespace App\Model\User\Service;


use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use App\Service\Email\AppTemplateCreator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class ResetTokenSender
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
     * @var AppTemplateCreator
     */
    private AppTemplateCreator $appTemplateCreator;

    /**
     * ResetTokenSender constructor.
     * @param MailerInterface $mailer
     * @param Environment $twig
     * @param AppTemplateCreator $appTemplateCreator
     */
    public function __construct(MailerInterface $mailer, Environment $twig, AppTemplateCreator $appTemplateCreator)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->appTemplateCreator = $appTemplateCreator;
    }

    /**
     * @param Email $email
     * @param ResetToken $token
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function send(Email $email, ResetToken $token)
    {
        $email = $this->appTemplateCreator
            ->getTemplate()
            ->to($email->getValue())
            ->html($this->twig->render('mail/user/reset.html.twig', [
                'token' => $token->getToken()
            ]));

        $this->mailer->send($email);
    }
}