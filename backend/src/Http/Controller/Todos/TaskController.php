<?php

declare(strict_types=1);

namespace App\Http\Controller\Todos;

use App\Domain\Auth\User\Entity\User\User;
use App\Domain\Todos\Entity\Task\Task;
use App\Domain\Todos\Read\Task\TaskFetcher;
use App\Domain\Todos\UseCase;
use App\Infrastructure\Security\Voter\Task\TaskAccess;
use App\Infrastructure\Upload\UploadHelper;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mime\MimeTypesInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
    private ValidatorInterface $validator;
    private MimeTypesInterface $mimeTypes;

    /**
     * TaskController constructor.
     * @param ValidatorInterface $validator
     * @param MimeTypesInterface $mimeTypes
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ValidatorInterface $validator,
        MimeTypesInterface $mimeTypes,
        LoggerInterface $logger,
        TranslatorInterface $translator
    )
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->validator = $validator;
        $this->mimeTypes = $mimeTypes;
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
        $filter = new \App\Domain\Todos\Read\Task\Filter\Filter($user->getId()->getValue(), Task::STATUS_PUBLISHED);

        $form = $this->createForm(\App\Domain\Todos\Read\Task\Filter\Form::class, $filter);
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
        $filter = new \App\Domain\Todos\Read\Task\FilterBar\Filter($currentUser->getId(), Task::STATUS_PUBLISHED);

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
        $filter = new \App\Domain\Todos\Read\Task\FilterBar\Filter($currentUser->getId(), Task::STATUS_FULFILLED);

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
     * @Route("/bar/{id}/edit-by-modal", name=".bar.edit_by_modal", methods={"POST"})
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
     * @Route("/user/{id}/add-by-modal", name=".user.add_by_modal", methods={"POST"})
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
     * @Route("/ajax-delete/{id}", name=".delete_by_modal", methods={"POST"})
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
     * @Route("/ajax-fulfilled/{id}", name=".fulfilled_by_modal", methods={"POST"})
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
     * @Route("/ajax-published/{id}", name=".published_by_modal", methods={"POST"})
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

    /**
     * @Route("/{id}/file", name=".file", methods={"POST"})
     * @param Request $request
     * @param Task $task
     * @param UseCase\File\Attach\Handler $handler
     * @return Response
     */
    public function uploadTaskFile(Request $request, Task $task, UseCase\File\Attach\Handler $handler): Response
    {
       $uploadedFile = $request->files->get('file');

       $violations = $this->validator->validate($uploadedFile, [
           new NotBlank(),
           new File([
               'maxSize' => '5M',
               'mimeTypes' => \array_merge($this->mimeTypes->getMimeTypes('xlsx'), $this->mimeTypes->getMimeTypes('pdf'))
           ])
       ]);

       if ($violations->count() > 0) {
           $violation = $violations[0];
           return $this->json($violation->getMessage(), 422);
       }

       $command = new UseCase\File\Attach\Command($uploadedFile, $task->getId()->getValue());

       try {
           $handler->handle($command);
       } catch (\Exception $e) {
           return $this->json($e->getMessage(), 422);
       }

       return $this->json('File upload.');
    }

    /**
     * @Route("/{id}/files", name=".files", methods={"GET"})
     * @param Task $task
     * @return Response
     */
    public function taskFiles(Task $task): Response
    {
        return $this->json($task->getFiles(), 200, [], [
            'groups' => 'show_files'
        ]);
    }

    /**
     * @Route("/file-delete/{id}", name=".file-delete", methods={"DELETE"})
     * @param \App\Domain\Todos\Entity\Task\File $file
     * @param UseCase\File\Delete\Handler $handler
     * @return Request
     */
    public function taskFileDelete(
        \App\Domain\Todos\Entity\Task\File $file,
        UseCase\File\Delete\Handler        $handler
    ): Response
    {
        $command = new UseCase\File\Delete\Command($file->getId());

        try {
            $handler->handle($command);
            return $this->json('File deleted.', 200);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), 422);
        }
    }

    /**
     * @Route("/file-download/{id}", name=".file-download", methods={"GET"})
     * @param \App\Domain\Todos\Entity\Task\File $file
     * @param UploadHelper $uploadHelper
     * @return Response
     */
    public function taskFileDownload(
        \App\Domain\Todos\Entity\Task\File $file,
        UploadHelper                       $uploadHelper
    ): Response
    {
        $response = new StreamedResponse(function () use ($file, $uploadHelper) {
            if (! $filePath = $file->getFilePath()) {
                throw new \DomainException('Path to file not defined.');
            }

            $outputStream = \fopen('php://output', 'wb');
            $fileStream = $uploadHelper->readStream($filePath);

            \stream_copy_to_stream($fileStream, $outputStream);
        });

        $response->headers->set('Content-Type', $file->getMimeType());
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $file->getFilename()
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}