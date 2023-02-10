<?php

namespace App\Controller;


use App\Entity\Message;
use App\Entity\UserToUser;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Repository\UserToUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class FollowersController extends AbstractController
{
    public function __construct(
        private readonly UserToUserRepository $userToUserRepository,
        private readonly UserRepository $userRepository,
    )
    {
    }
    public function __invoke(Request $request)
    {

        $userId = $request->query->get('userId', null);
        if (!$userId ) {
           return new JsonResponse(['error' => 'userId is required'], 400);
        }

        $selectedUser = $this->userRepository->find($userId);

        if (!$selectedUser) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $page = $request->query->get('page', 1);
        if (!$page || $page < 1) {
            $page = 1;
        }

        $user = $this->getUser();

        $selectedUser = $this->userRepository->find($userId);

        $userToUsers = $this->userToUserRepository->getFollowers($selectedUser->getId(), 20, $page);


        $followers = [];
        foreach ($userToUsers as $userToUser) {
            $user = $userToUser->getMe();
            $user->setUserToUserId($userToUser->getId());
            $followers[] = $user;
        }

        foreach ($followers as $follower) {
            $userToUser = $this->userToUserRepository->findOneBy([
                'me' => $user->getId(),
                'other' => $follower->getId()
            ]);

            if ($userToUser) {
                $follower->setFollowed(true);
            }
        }

        return $followers;

    }
}
