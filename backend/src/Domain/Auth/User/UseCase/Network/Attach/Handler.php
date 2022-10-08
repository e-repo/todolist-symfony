<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Network\Attach;

use App\Domain\Auth\User\Entity\Network\Network;
use App\Domain\Auth\User\Entity\User\Email;
use App\Domain\Auth\User\Entity\User\Id;
use App\Domain\Auth\User\Entity\User\User;
use App\Domain\Auth\User\Repository\NetworkRepository;
use App\Domain\Auth\User\Repository\UserRepository;
use App\Domain\Service\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Handler
{
    private UserRepository $users;
    private Flusher $flusher;
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;
    private NetworkRepository $networks;

    /**
     * Handler constructor.
     * @param EntityManagerInterface $em
     * @param UserRepository $users
     * @param NetworkRepository $networks
     * @param TranslatorInterface $translator
     * @param Flusher $flusher
     */
    public function __construct(
        EntityManagerInterface $em,
        UserRepository $users,
        NetworkRepository $networks,
        TranslatorInterface $translator,
        Flusher $flusher
    )
    {
        $this->em = $em;
        $this->users = $users;
        $this->flusher = $flusher;
        $this->networks = $networks;
        $this->translator = $translator;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command)
    {
        $network = $this->networks->findByNetworkIdentity($command->network, $command->networkIdentity);
        $userByNetwork = $network ? $network->getUser() : null;

        $isUserAndEmailExist = $userByNetwork && $userByNetwork->getEmail() instanceof Email;
        if ($isUserAndEmailExist) {
            throw  new \DomainException($this->translator->trans('Profile is already in use.', [], 'profile'));
        }

        $user = $this->users->getById(new Id($command->uuid));
        $userHasNoEmail = $userByNetwork && $userByNetwork->getEmail() === null;
        if ($userHasNoEmail) {
            $this->overrideUserNetworks($userByNetwork->getId(), $user);
            $this->em->remove($userByNetwork);
            $this->flusher->flush();
            return;
        }

        $user->attachNetwork($command->network, $command->networkIdentity);
        $this->em->persist($user);
        $this->flusher->flush();
    }

    private function overrideUserNetworks(Id $searchUserId, User $user): void
    {
        /**
         * @var  Network $network
         */
        foreach ($this->networks->findByUserId($searchUserId) as $network) {
            $network->changeUser($user);
            $this->em->persist($network);
        }
        $this->flusher->flush();
    }
}