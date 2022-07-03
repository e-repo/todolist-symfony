<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Voter\Task;

use App\Domain\Auth\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\RuntimeException;

class TaskAccess extends Voter
{
    public const ADD = 'add';
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    public const REVOKE = 'revoke';
    public const FULFILLED = 'fulfilled';

    protected function supports($attribute, $subject): bool
    {
        return \in_array($attribute, $this->actionList()) && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $methodName = \sprintf('%sChecker', $attribute);

        if (! \method_exists($this, $methodName)) {
            throw new RuntimeException(\sprintf('Method "%s" is\'t allowed.', $methodName));
        }

        return $this->$methodName($subject, $token);
    }

    private function actionList(): array
    {
        return  [
            self::ADD,
            self::EDIT,
            self::DELETE,
            self::REVOKE,
            self::FULFILLED
        ];
    }


    protected function editChecker($subject, TokenInterface $token): bool
    {
        return $this->checkUser($subject, $token);
    }

    protected function addChecker($subject, TokenInterface $token): bool
    {
        return $this->checkUser($subject, $token);
    }

    protected function deleteChecker($subject, TokenInterface $token): bool
    {
        return $this->checkUser($subject, $token);
    }

    protected function revokeChecker($subject, TokenInterface $token): bool
    {
        return $this->checkUser($subject, $token);
    }

    protected function fulfilledChecker($subject, TokenInterface $token): bool
    {
        return $this->checkUser($subject, $token);
    }

    private function checkUser(User $user, TokenInterface $token): bool
    {
        return $user->getId()->getValue() === $token->getUser()->getId();
    }
}