<?php

namespace App\Domain\Auth\User\UseCase\SignUp\Request;

use App\Domain\Auth\User\Entity\User\Email;
use App\Domain\Auth\User\Entity\User\Id;
use App\Domain\Auth\User\Entity\User\Name;
use App\Domain\Auth\User\Entity\User\User;
use App\Domain\Auth\User\Read\UserFetcher;
use App\Domain\Auth\User\Service\PasswordHasher;
use App\Domain\Auth\User\Service\SignUpConfirmTokenizer;
use App\Domain\Auth\User\Service\SignUpConfirmTokenSender;
use App\Domain\Service\Flusher;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var UserFetcher
     */
    private UserFetcher $userFetcher;
    /**
     * @var PasswordHasher
     */
    private PasswordHasher $hasher;
    /**
     * @var SignUpConfirmTokenizer
     */
    private SignUpConfirmTokenizer $tokenizer;
    /**
     * @var Flusher
     */
    private Flusher $flusher;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;
    /**
     * @var SignUpConfirmTokenSender
     */
    private SignUpConfirmTokenSender $sender;

    /**
     * Handler constructor.
     * @param EntityManagerInterface $em
     * @param UserFetcher $userFetcher
     * @param PasswordHasher $hasher
     * @param SignUpConfirmTokenizer $tokenizer
     * @param Flusher $flusher
     * @param SignUpConfirmTokenSender $sender
     */
    public function __construct(
        EntityManagerInterface $em,
        UserFetcher $userFetcher,
        PasswordHasher $hasher,
        SignUpConfirmTokenizer $tokenizer,
        Flusher $flusher,
        SignUpConfirmTokenSender $sender
    )
    {
        $this->userFetcher = $userFetcher;
        $this->hasher = $hasher;
        $this->tokenizer = $tokenizer;
        $this->flusher = $flusher;
        $this->em = $em;
        $this->sender = $sender;
    }

    /**
     * @param Command $command
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function handle(Command $command)
    {
        $email = new Email($command->email);

        if ($this->userFetcher->hasByEmail($email->getValue())) {
            throw new \DomainException('User already exist.');
        }

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Name($command->firstName, $command->lastName),
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokenizer->generate()
        );

        $this->em->persist($user);
        $this->flusher->flush();
        $this->sender->send($email, $token);
    }
}