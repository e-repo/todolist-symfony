<?php

declare(strict_types=1);

namespace App\Http\Controller\User\Profile;

use App\Domain\Auth\Entity\User\Id;
use App\Domain\Auth\Entity\User\UserRepository;
use App\Domain\Auth\Service\NewEmailConfirmTokenizer;
use App\Domain\Auth\UseCase\Email;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/profile/email")
 * Class EmailController
 * @package App\Controller\Profile
 */
class EmailController extends AbstractController
{
    private LoggerInterface $logger;
    private UserRepository $userRepository;

    /**
     * EmailController constructor.
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     */
    public function __construct(
        LoggerInterface $logger,
        UserRepository $userRepository
    )
    {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("", name="profile.email")
     * @param Request $request
     * @param Email\Request\Handler $handler
     * @param NewEmailConfirmTokenizer $tokenizer
     * @return Response
     */
    public function request(
        Request $request,
        Email\Request\Handler $handler,
        NewEmailConfirmTokenizer $tokenizer
    ): Response
    {
        $token = $tokenizer->generate();
        $confirmUrl = $this->generateUrl(
            'profile.email_confirm',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $command = (new Email\Request\Command())
            ->setId($this->getUser()->getId())
            ->setToken($token)
            ->setConfirmUrl($confirmUrl);

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
     * @IsGranted("ROLE_USER")
     * @Route("/confirm/{token}", name="profile.email_confirm")
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

    /**
     * @Route("/confirm-page/{token}", name="profile.email_confirm_page")
     * @param $token
     * @param Email\Confirm\Handler $handler
     * @param Request $request
     * @return Response
     */
    public function confirmWithRedirectToPage($token, Email\Confirm\Handler $handler, Request $request): Response
    {
        try {
            $user = $this->userRepository->get(
                new Id($request->query->get('user_id'))
            );
        } catch (\Exception $e) {
            throw new NotFoundHttpException('User not found.');
        }

        $command = new Email\Confirm\Command($user->getId()->getValue(), $token);
        $pathToUi = $this->getParameter('ui_host');

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Email is successfully changed.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('app/profile/confirm-email.html.twig', compact('pathToUi'));
    }
}