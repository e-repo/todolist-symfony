<?php

declare(strict_types=1);

namespace App\Controller\Todos;

use App\Model\Todos\Entity\Task\Task;
use App\Model\Todos\UseCase;
use App\Model\User\Entity\User\User;
use App\ReadModel\Task\Filter;
use App\ReadModel\Task\FilterBar;
use App\ReadModel\Task\TaskFetcher;
use App\Security\Voter\Task\TaskAccess;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/tasks", name="tasks")
 * Class TaskController
 * @package App\Controller\Todos
 */
class TaskController extends AbstractController
{
    private const PER_PAGE = 20;
    private const PER_PAGE_FOR_BAR = 12;

    private LoggerInterface $logger;
    private TranslatorInterface $translator;

    /**
     * TaskController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/user/{id}", name=".user")
     * @param User $user
     * @param Request $request
     * @param TaskFetcher $fetcher
     * @return Response
     */
    public function index(User $user, Request $request, TaskFetcher $fetcher): Response
    {
        $filter = new Filter\Filter($user->getId()->getValue(), Task::STATUS_PUBLISHED);

        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $fetcher->all(
            $filter,
            $request->get('page', 1),
            self::PER_PAGE,
            $request->get('sort', 'date'),
            $request->get('direction', 'desc')
        );

        return $this->render('app/todos/index.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/bar/published", name=".bar.published")
     * @param Request $request
     * @param TaskFetcher $fetcher
     * @return Response
     */
    public function barPublished(Request $request, TaskFetcher $fetcher): Response
    {
        $currentUser = $this->getUser();
        $filter = new FilterBar\Filter($currentUser->getId(), Task::STATUS_PUBLISHED);

        $pagination = $fetcher->allForBar(
            $filter,
            $request->get('page', 1),
            $request->get('size', self::PER_PAGE_FOR_BAR)
        );

        return $this->render('app/todos/task-bar.html.twig', [
            'user' => $currentUser,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/bar/fulfilled", name=".bar.fulfilled")
     * @param Request $request
     * @param TaskFetcher $fetcher
     * @return Response
     */
    public function barFulfilled(Request $request, TaskFetcher $fetcher): Response
    {
        $currentUser = $this->getUser();
        $filter = new FilterBar\Filter($currentUser->getId(), Task::STATUS_FULFILLED);

        $pagination = $fetcher->allForBar(
            $filter,
            $request->get('page', 1),
            $request->get('size', self::PER_PAGE_FOR_BAR)
        );

        return $this->render('app/todos/task-bar.html.twig', [
            'user' => $currentUser,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/edit/{id}", name=".edit")
     * @param Task $task
     * @param Request $request
     * @param UseCase\Update\Handler $handler
     * @return Response
     */
    public function edit(Task $task, Request $request, UseCase\Update\Handler $handler): Response
    {
        $command = new UseCase\Update\Command();
        $command->createFromTask($task);

        $form = $this->createForm(UseCase\Update\Form::class, $command, ['taskId' => $task->getId()->getValue()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Task edited successfully.', [], 'task'));
                return $this->redirectToRoute('tasks.user', ['id' => $task->getUser()->getId()->getValue()]);
            } catch (\Exception $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('tasks.user', ['id' => $task->getUser()->getId()->getValue()]);
            }
        };

        return $this->render('app/todos/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/bar/{id}/edit-by-modal", name=".bar.edit-by-modal", methods={"POST"})
     * @param Task $task
     * @param Request $request
     * @param UseCase\Update\Handler $handler
     * @return Response
     */
    public function editByModal(Task $task, Request $request, UseCase\Update\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::EDIT, $task->getUser());

        $command = new UseCase\Update\Command();
        $command->createFromTask($task);

        $form = $this->createForm(UseCase\Update\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Task edited successfully.', [], 'task'));
                return new JsonResponse(['error' => '']);
            } catch (\Exception $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('warning', $e->getMessage());
                return new JsonResponse(['error' => $e->getMessage()]);
            }
        };

        return new JsonResponse([
            'formTitle' => $this->translator->trans('Edit Tasks', [], 'task'),
            'form' => $this->renderView('app/todos/modals/_task-edit.html.twig', [
                'form' => $form->createView(),
            ]),
            'formButtons' => $this->renderView('app/todos/modals/_task-edit-buttons.html.twig'),
        ]);
    }

    /**
     * @Route("/user/{id}/add-by-modal", name=".user.add-by-modal", methods={"POST"})
     * @param User $user
     * @param Request $request
     * @param UseCase\Create\Handler $handler
     * @return Response
     */
    public function addByModal(User $user, Request $request, UseCase\Create\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::ADD, $user);

        $command = new UseCase\Create\Command();
        $command->userId = $user->getId()->getValue();

        $form = $this->createForm(UseCase\Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('User task added.', [], 'task'));
                return new JsonResponse(['error', '']);
            } catch (\Exception $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('warning', $e->getMessage());
                return new JsonResponse(['error', $e->getMessage()]);
            }
        };

        return new JsonResponse([
            'formTitle' => $this->translator->trans('Add task', [], 'task'),
            'form' => $this->renderView('app/todos/modals/_task-add.html.twig', [
                'form' => $form->createView(),
            ]),
            'formButtons' => $this->renderView('app/todos/modals/_task-add-buttons.html.twig'),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/user/{id}/create", name=".create")
     * @param User $user
     * @param Request $request
     * @param UseCase\Create\Handler $handler
     * @return Response
     */
    public function create(User $user, Request $request, UseCase\Create\Handler $handler): Response
    {
        $command = new UseCase\Create\Command();

        $form = $this->createForm(UseCase\Create\Form::class, $command, ['userId' => $user->getId()->getValue()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('User task added.', [], 'task'));
                return $this->redirectToRoute('users.show', ['id' => $user->getId()->getValue()]);
            } catch (\Exception $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('warning', $e->getMessage());
            }
        }

        return $this->render('app/todos/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/delete/{id}", name=".delete", methods={"POST"})
     * @param Task $task
     * @param UseCase\Delete\Handler $handler
     * @return Response
     */
    public function delete(Task $task, UseCase\Delete\Handler $handler): Response
    {
        $command = new UseCase\Delete\Command($task->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', $this->translator->trans('Task deleted successfully.', [], 'task'));
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('warning', $e->getMessage());
        }

        return $this->redirectToRoute('tasks.user', ['id' => $task->getUser()->getId()->getValue()]);
    }

    /**
     * @Route("/ajax-delete/{id}", name=".delete-by-modal", methods={"POST"})
     *
     * @param Task $task
     * @param UseCase\Delete\Handler $handler
     * @return Response
     */
    public function deleteByModal(Task $task, UseCase\Delete\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::DELETE, $task->getUser());
        $command = new UseCase\Delete\Command($task->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', $this->translator->trans('Task deleted successfully.', [], 'task'));
            $errorMessage = '';
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('warning', $e->getMessage());
            $errorMessage = $e->getMessage();
        }

        return new JsonResponse(['error' => $errorMessage]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/fulfilled/{id}", name=".fulfilled")
     * @param Task $task
     * @param UseCase\Fulfilled\Handler $handler
     * @return Response
     */
    public function fulfilled(Task $task, UseCase\Fulfilled\Handler $handler): Response
    {
        $command = new UseCase\Fulfilled\Command($task->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', $this->translator->trans('Task fulfilled successfully.', [], 'task'));
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('warning', $e->getMessage());
            $this->addFlash('warning', $e->getMessage());
        }

        return $this->redirectToRoute('tasks.user', ['id' => $task->getUser()->getId()->getValue()]);
    }

    /**
     * @Route("/ajax-fulfilled/{id}", name=".fulfilled-by-modal", methods={"POST"})
     * @param Task $task
     * @param UseCase\Fulfilled\Handler $handler
     * @return Response
     */
    public function fulfilledByModal(Task $task, UseCase\Fulfilled\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::FULFILLED, $task->getUser());
        $command = new UseCase\Fulfilled\Command($task->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', $this->translator->trans('Task fulfilled successfully.', [], 'task'));
            $errorMessage = '';
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('warning', $e->getMessage());
            $errorMessage = $e->getMessage();
        }

        return new JsonResponse(['error' => $errorMessage]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/published/{id}", name=".published")
     * @param Task $task
     * @param UseCase\Published\Handler $handler
     * @return Response
     */
    public function published(Task $task, UseCase\Published\Handler $handler): Response
    {
        $command = new UseCase\Published\Command($task->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', $this->translator->trans('Task published successfully.', [], 'task'));
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('warning', $e->getMessage());
        }

        return $this->redirectToRoute('tasks.user', ['id' => $task->getUser()->getId()->getValue()]);
    }

    /**
     * @Route("/ajax-published/{id}", name=".published-by-modal", methods={"POST"})
     * @param Task $task
     * @param UseCase\Published\Handler $handler
     * @return Response
     */
    public function publishedByModal(Task $task, UseCase\Published\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::REVOKE, $task->getUser());
        $command = new UseCase\Published\Command($task->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', $this->translator->trans('Task published successfully.', [], 'task'));
            $errorMessage = '';
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('warning', $e->getMessage());
            $errorMessage = $e->getMessage();
        }

        return new JsonResponse(['error' => $errorMessage]);
    }
}