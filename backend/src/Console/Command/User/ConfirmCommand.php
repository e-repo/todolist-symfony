<?php

declare(strict_types=1);

namespace App\Console\Command\User;

use App\Domain\Auth\User\Read\UserFetcher;
use App\Domain\User\UseCase\SignUp\Confirm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ConfirmCommand extends Command
{
    /**
     * @var UserFetcher
     */
    private UserFetcher $users;
    /**
     * @var \App\Domain\Auth\User\UseCase\SignUp\Confirm\Manual\Handler
     */
    private \App\Domain\Auth\User\UseCase\SignUp\Confirm\Manual\Handler $handler;

    /**
     * ConfirmCommand constructor.
     * @param UserFetcher $users
     * @param \App\Domain\Auth\User\UseCase\SignUp\Confirm\Manual\Handler $handler
     */
    public function __construct(UserFetcher $users, \App\Domain\Auth\User\UseCase\SignUp\Confirm\Manual\Handler $handler)
    {
        $this->users = $users;
        $this->handler = $handler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:confirm')
            ->setDescription('Confirms signed up user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $email = $helper->ask($input, $output, new Question('Email: '));

        if (!$user = $this->users->findByEmail($email)) {
            throw new \LogicException('User is not found.');
        }

        $command = new \App\Domain\Auth\User\UseCase\SignUp\Confirm\Manual\Command($user->id);
        $this->handler->handle($command);

        $output->writeln('<info>Done!</info>');
    }
}