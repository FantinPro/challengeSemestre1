<?php

namespace App\Controller;

use App\Service\PaymentService;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class PremiumSubscriptionController extends AbstractController
{
    public function __construct(
        private readonly PaymentService $paymentService
    )
    {
    }

    #[Route('/api/premium_subscription', name: 'premium_subscription', methods: ['GET'])]
    public function getSubscribtionPK()
    {
        if (!$this->getUser()) {
            throw new HttpException(401, 'Unauthorized');
        }
        return new JsonResponse($this->paymentService->createPremiumSubscription($this->getUser()));
    }
}