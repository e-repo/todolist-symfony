<?php

namespace App\Controller\User;

use App\Model\User\UseCase;
use App\ReadModel\User\Filter;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Translator;
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
}
