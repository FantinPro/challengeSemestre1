<?php

namespace App\Security\Voter;

use App\Entity\UserToUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserToUserVoter extends Voter
{

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        $supportsAttribute = in_array($attribute, ['USER_TO_USER_CREATE']);
        $supportsSubject = $subject instanceof UserToUser;

        return $supportsAttribute && $supportsSubject;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /**
         * @var UserToUser $userToUser
         */
        $userToVoter = $subject;

        switch ($attribute) {
            case 'USER_TO_USER_CREATE':
                return $userToUser->getMe()->getId() === $user->getId();
        }

        return false;
    }
}
