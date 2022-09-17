<?php

declare(strict_types=1);

namespace App\Http\Controller\Auth;

use App\Domain\Auth\User\UseCase;
use App\Domain\Auth\User\Read\UserFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * ResetController constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/reset", name="auth.reset")
     * @param Request $request
     * @param UseCase\Reset\Request\Handler $handler
     * @return Response
     */
    public function request(Request $request, UseCase\Reset\Request\Handler $handler): Response
    {
        $command = new UseCase\Reset\Request\Command();

        $form = $this->createForm(UseCase\Reset\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/auth/reset/request.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset/{token}", name="auth.reset_reset")
     * @param string $token
     * @param Request $request
     * @param UseCase\Reset\Reset\Handler $handler
     * @param UserFetcher $users
     * @return Response
     */
    public function reset(
        string                                            $token,
        Request                                           $request,
        UseCase\Reset\Reset\Handler $handler,
        UserFetcher                                       $users
    ): Response
    {
        if (! $users->existByResetToken($token)) {
            $this->addFlash('error', 'Incorrect or already confirmed token');
            return $this->redirectToRoute('home');
        }

        $command = new UseCase\Reset\Reset\Command($token);

        $form = $this->createForm(UseCase\Reset\Reset\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Password is successfully changed.');
                return $this->redirectToRoute('app_login');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/auth/reset/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
}