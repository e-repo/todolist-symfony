<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Model\User\UseCase\Email;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/profile/email")
 * Class EmailController
 * @package App\Controller\Profile
 */
class EmailController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * EmailController constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("", name="profile.email")
     * @param Request $request
     * @param Email\Request\Handler $handler
     * @return Response
     */
    public function request(Request $request, Email\Request\Handler $handler): Response
    {
        $command = new Email\Request\Command($this->getUser()->getId());

        $form = $this->createForm(Email\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');
                return $this->redirectToRoute('profile');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/profile/email.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{token}", name="profile.email_confirm")
     * @param $token
     * @param Email\Confirm\Handler $handler
     * @return Response
     */
    public function confirm($token, Email\Confirm\Handler $handler): Response
    {
        $command = new Email\Confirm\Command($this->getUser()->getId(), $token);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Email is successfully changed.');
            return $this->redirectToRoute('profile');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception', $e]);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('profile');
        }
    }
}