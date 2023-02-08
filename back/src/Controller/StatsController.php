<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class StatsController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private AdRepository $adRepository,
        private MessageRepository $messageRepository,
    )
    {}

    #[Route('/api/stats', name: 'app_stats', methods: ['GET'])]
    public function getStats(Request $request) {
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        // number registered users
        $nbUsers = $this->userRepository->countUsersBetween($startDate, $endDate);

        // number ads
        $nbAds = $this->adRepository->countAdsBetween($startDate, $endDate);

        // number messages
        $nbMessages = $this->messageRepository->countMessagesBetween($startDate, $endDate);

        // get Amount of money earned by ads
        $amountEarned = $this->adRepository->getAmountEarned($startDate, $endDate);

        return new JsonResponse([
            'nbUsers' => $nbUsers,
            'nbAds' => $nbAds,
            'nbMessages' => $nbMessages,
            'amountEarned' => $amountEarned
        ]);
    }
}
