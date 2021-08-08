<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('app/user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
