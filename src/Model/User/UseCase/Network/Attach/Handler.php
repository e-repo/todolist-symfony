<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Attach;

use App\Model\User\Entity\User\UserRepository;
use App\ReadModel\User\UserFetcher;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\User\Entity\User\Id;
use App\Model\Flusher;
use Symfony\Contracts\Translation\TranslatorInterface;

class Handler
{
    private UserRepository $users;
    private Flusher $flusher;
    private EntityManagerInterface $em;
    private UserFetcher $fetcher;
    private TranslatorInterface $translator;

    /**
     * Handler constructor.
     * @param EntityManagerInterface $em
     * @param UserRepository $users
     * @param UserFetcher $fetcher
     * @param TranslatorInterface $translator
     * @param Flusher $flusher
     */
    public function __construct(
        EntityManagerInterface $em,
        UserRepository $users,
        UserFetcher $fetcher,
        TranslatorInterface $translator,
        Flusher $flusher
    )
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->em = $em;
        $this->fetcher = $fetcher;
        $this->translator = $translator;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command)
    {
        if ($this->fetcher->hasByNetworkIdentity($command->network, $command->networkIdentity)) {
            throw  new \DomainException($this->translator->trans('Profile is already in use.', [], 'profile'));
        }

        $user = $this->users->get(new Id($command->uuid));
        $user->attachNetwork($command->network, $command->networkIdentity);

        $this->em->persist($user);
        $this->flusher->flush();
    }
}