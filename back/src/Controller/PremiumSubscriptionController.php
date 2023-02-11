<?php

namespace App\Controller;

use App\Service\PaymentService;
use http\Env\Request;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
class PremiumSubscriptionController extends AbstractController
{
    public function __construct(
        private readonly PaymentService $paymentService
    )
    {
    }

    #[Route('/api/premium_subscription', name: 'premium_subscription', methods: ['GET'])]
    public function createSubscriptionLink(): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Unauthorized', 401);
        }
        return new JsonResponse($this->paymentService->createPremiumSubscription($this->getUser()));
    }
}