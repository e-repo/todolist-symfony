<?php

declare(strict_types=1);

namespace App\Http\Controller\User\Profile;

use App\Domain\User\UseCase\Name;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/profile/name")
 * Class NameController
 * @package App\Controller\Profile
 */
class NameController extends AbstractController
{
    private LoggerInterface $logger;
    private TranslatorInterface $translator;

    /**
     * NameController constructor.
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     */
    public function __construct(
        TranslatorInterface $translator,
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @Route("/{id}", name="profile.change_name")
     * @param string $id
     * @param Request $request
     * @param Name\Handler $handler
     * @return Response
     */
    public function changeUserName(string $id, Request $request, Name\Handler $handler): Response
    {
        $command = new Name\Command($id);

        $form = $this->createForm(Name\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans("User's first and last name updated.", [], 'profile'));
                return $this->redirectToRoute('profile');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/profile/name.html.twig', [
            'form' => $form->createView()
        ]);
    }
}