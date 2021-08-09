<?php

namespace App\Controller\User;

use App\ReadModel\User\Filter;
use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users", name="users")
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
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

        dd($form);

//        return $this->render('app/user/index.html.twig', [
//            'controller_name' => 'UserController',
//        ]);
    }
}
