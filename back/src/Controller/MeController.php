<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class MeController extends AbstractController
{

    public function __construct(
        private UserRepository $userRepository
    )
    {}

    public function __invoke(Request $request)
    {
        $user = $this->userRepository->findOneBy([
            'pseudo' => $request->query->get('pseudo')
        ]);
        if ($user === null) {
            return $this->json([
                'error' => 'User not found'
            ], 404);
        }
        return $user;
    }
}
