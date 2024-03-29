<?php

declare(strict_types=1);

namespace App\Http\Controller\Auth;

use App\Domain\Auth\User\UseCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SignUpController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * SignUpController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @Route("/signup", name="auth.signup")
     * @param Request $request
     * @param UseCase\SignUp\Request\Handler $handler
     * @return Response
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function request(Request $request, UseCase\SignUp\Request\Handler $handler): Response
    {
        $command = new UseCase\SignUp\Request\Command();

        $form = $this->createForm(UseCase\SignUp\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Check your email.', [], 'notification'));
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/auth/signup.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/signup/{token}", name="auth.signup_confirm")
     * @param string $token
     * @param UseCase\SignUp\Confirm\ByToken\Handler $handler
     * @return Response
     */
    public function confirm(string $token, UseCase\SignUp\Confirm\ByToken\Handler $handler): Response
    {
        $command = new UseCase\SignUp\Confirm\ByToken\Command($token);

        try {
            $handler->handle($command);
            $this->addFlash('success', $this->translator->trans('Email is success confirmed.', [], 'notification'));
            return $this->redirectToRoute('home');
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception', $e]);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('home');
        }
    }
}