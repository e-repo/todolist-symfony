<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Attach;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Network;
use App\Model\User\Entity\User\NetworkRepository;
use App\Model\User\Entity\User\User;
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
        $userByNetwork = $this->networks
                ->findByNetworkIdentity($command->network, $command->networkIdentity)->getUser() ?? null;

        if ($userByNetwork && $userByNetwork->getEmail() instanceof Email) {
            throw  new \DomainException($this->translator->trans('Profile is already in use.', [], 'profile'));
        }

        $user = $this->users->get(new Id($command->uuid));
        if ($userByNetwork && $userByNetwork->getEmail() === null) {
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
    }
}