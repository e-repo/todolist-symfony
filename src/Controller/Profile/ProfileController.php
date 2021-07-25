<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\NetworkRepository;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private UserRepository $users;
    private NetworkRepository $networks;

    /**
     * ProfileController constructor.
     * @param UserRepository $users
     * @param NetworkRepository $networks
     */
    public function __construct(UserRepository $users, NetworkRepository $networks)
    {
        $this->users = $users;
        $this->networks = $networks;
    }

    /**
     * @Route("/profile", name="profile")
     * @return Response
     */
    public function index(): Response
    {
        $user = $this->users->get(new Id($this->getUser()->getId()));
        return $this->render('app/profile/show.html.twig', compact('user'));
    }
}