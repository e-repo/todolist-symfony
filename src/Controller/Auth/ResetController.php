<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Model\User\UseCase\Reset;
use App\ReadModel\User\UserFetcher;
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
     * @param Reset\Request\Handler $handler
     * @return Response
     */
    public function request(Request $request, Reset\Request\Handler $handler): Response
    {
        $command = new Reset\Request\Command();

        $form = $this->createForm(Reset\Request\Form::class, $command);
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
     * @Route("/reset/{token}", name="auth.reset.reset")
     * @param string $token
     * @param Request $request
     * @param Reset\Reset\Handler $handler
     * @param UserFetcher $users
     * @return Response
     */
    public function reset(
        string $token,
        Request $request,
        Reset\Reset\Handler $handler,
        UserFetcher $users
    ): Response
    {
        if (! $users->existByResetToken($token)) {
            $this->addFlash('error', 'Incorrect or already confirmed token');
            $this->redirectToRoute('home');
        }

        $command = new Reset\Reset\Command($token);

        $form = $this->createForm(Reset\Reset\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Password is successfully changed.');
                $this->redirectToRoute('app_login');
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