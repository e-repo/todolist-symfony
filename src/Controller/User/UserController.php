<?php

namespace App\Controller\User;

use App\Model\User\Entity\User\User;
use App\Model\User\UseCase;
use App\ReadModel\Task\TaskFetcher;
use App\ReadModel\User\Filter;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/users", name="users")
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    private const PER_PAGE = 50;
    private TranslatorInterface $translator;
    private LoggerInterface $logger;

    /**
     * UserController constructor.
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     */
    public function __construct(TranslatorInterface $translator, LoggerInterface $logger)
    {
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @Route("", name="")
     * @param Request $request
     * @param UserFetcher $fetcher
     * @return Response
     */
    public function index(Request $request, UserFetcher $fetcher): Response
    {
        $filter = new Filter\Filter();

        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $fetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'date'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/user/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/create", name=".create")
     * @param Request $request
     * @param UseCase\Create\Handler $handler
     * @return Response
     */
    public function create(Request $request, UseCase\Create\Handler $handler): Response
    {
        $command = new UseCase\Create\Command();

        $form = $this->createForm(UseCase\Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('User created.'));
                return $this->redirectToRoute('users');
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name=".edit")
     * @param User $user
     * @param Request $request
     * @param UseCase\Edit\Handler $handler
     * @return Response
     */
    public function edit(User $user, Request $request, UseCase\Edit\Handler $handler): Response
    {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', $this->translator->trans('Unable to edit yourself.'));
            return $this->redirectToRoute('users.show', ['id' => $user->getId()->getValue()]);
        }

        $command = UseCase\Edit\Command::createFromUser($user);
        $form = $this->createForm(UseCase\Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('User is success edit.'));
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }

            return $this->redirectToRoute('users.show', ['id' => $user->getId()->getValue()]);
        }

        return $this->render('app/user/edit.html.twig', [
            'form' =>  $form->createView(),
        ]);
    }

    /**
     * @Route("/role/{id}", name=".role")
     * @param User $user
     * @param Request $request
     * @param UseCase\Role\Change\Handler $handler
     * @return Response
     */
    public function changeRole(User $user, Request $request, UseCase\Role\Change\Handler $handler): Response
    {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', $this->translator->trans('Unable to edit yourself.'));
            return $this->redirectToRoute('users.show', ['id' => $user->getId()->getValue()]);
        }

        $command = UseCase\Role\Change\Command::createFromUser($user);
        $form = $this->createForm(UseCase\Role\Change\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isRequired()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('User is success edit.'));
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }

            return $this->redirectToRoute('users.show', ['id' => $user->getId()->getValue()]);
        }

        return $this->render('app/user/change-role.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/block/{id}", name=".block", methods={"POST"})
     * @param User $user
     * @param Request $request
     * @param UseCase\Block\Handler $handler
     * @return Response
     */
    public function block(User $user, Request $request, UseCase\Block\Handler $handler): Response
    {
        if (! $this->isCsrfTokenValid('block', $request->request->get('token'))) {
            $this->addFlash('error', $this->translator->trans('Invalid csrf token.'));
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', $this->translator->trans('Unable to block yourself.'));
            return $this->redirectToRoute('users.show', ['id' => $user->getId()->getValue()]);
        }

        try {
            $command = new UseCase\Block\Command($user->getId()->getValue());
            $handler->handle($command);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }
    /**
     * @Route("/active/{id}", name=".active", methods={"POST"})
     * @param User $user
     * @param Request $request
     * @param UseCase\Activate\Handler $handler
     * @return Response
     */
    public function active(User $user, Request $request, UseCase\Activate\Handler $handler): Response
    {
        if (! $this->isCsrfTokenValid('active', $request->request->get('token'))) {
            $this->addFlash('error', $this->translator->trans('Invalid csrf token.'));
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        try {
            $command = new UseCase\Activate\Command($user->getId()->getValue());
            $handler->handle($command);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }

    /**
     * @Route("/{id}", name=".show")
     * @param User $user
     * @param Request $request
     * @param TaskFetcher $fetcher
     * @return Response
     */
    public function show(User $user, Request $request, TaskFetcher $fetcher): Response
    {
        $numberTasks = $fetcher->numberUserTasks($user->getId()->getValue());
        return $this->render('app/user/show.html.twig', [
            'user' => $user,
            'numberTasks' => $numberTasks,
        ]);
    }
}
