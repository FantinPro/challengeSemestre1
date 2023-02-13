<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\UserToUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class MeController extends AbstractController
{

    public function __construct(
        private UserRepository $userRepository,
        private UserToUserRepository $userToUserRepository,
    )
    {}

    public function __invoke(Request $request)
    {
        $user = $this->userRepository->findOneBy([
            'pseudo' => $request->query->get('pseudo')
        ]);

        $currentUser = $this->getUser();

        $userToUser = $this->userToUserRepository->findOneBy([
            'me' => $currentUser,
            'other' => $user
        ]);

        if ($userToUser) {
            $user->setFollowed(true);
        }

        if ($user === null) {
            return $this->json([
                'error' => 'User not found'
            ], 404);
        }
        return $user;
    }
}
