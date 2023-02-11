<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

class PaymentService
{
    private \Stripe\StripeClient $stripe;

    public function __construct(private string $stripePK, private string $premiumPriceId,private readonly EntityManagerInterface $em)
    {
        \Stripe\Stripe::setApiKey($stripePK);
    }

    public function createPremiumSubscription(UserInterface $user): string
    {

            $checkout_session = \Stripe\Checkout\Session::create([
                'line_items' => [
                    [
                        'price' => $this->premiumPriceId,
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'subscription',
                'success_url' => 'http://localhost:3000/success',
                'cancel_url' => 'http://localhost:3000/cancel',
                'customer_email' => $user->getEmail(),
                'client_reference_id' => $user->getId(),
            ]);

            dd($checkout_session);

            return $checkout_session->url;
    }

}

