<?php


namespace App\Model\User\UseCase\SignUp\Confirm\Manual;


use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
    /**
     * @var UserRepository
     */
    private UserRepository $users;
    /**
     * @var Flusher
     */
    private Flusher $flusher;

    /**
     * Handler constructor.
     * @param UserRepository $users
     * @param Flusher $flusher
     */
    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));
        $user->confirmSignUp();
        $this->flusher->flush();
    }
}