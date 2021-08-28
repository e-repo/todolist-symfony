<?php

declare(strict_types=1);

namespace App\Controller\Todos;

use App\Model\Todos\Entity\Task\Task;
use App\Model\Todos\UseCase;
use App\Model\User\Entity\User\User;
use App\ReadModel\Task\Filter;
use App\ReadModel\Task\TaskFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/user/{id}/create", name=".create")
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
}