<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\DBAL\Driver\OCI8\Exception\Error;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Subscription;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

class PaymentService
{
    private \Stripe\StripeClient $stripe;

    public function __construct(private string $stripePK, private string $premiumPriceId, private string $front_end_url, private readonly EntityManagerInterface $em)
    {
        \Stripe\Stripe::setApiKey($stripePK);
    }



    private function getStripeCustomerId(UserInterface $user) {
        if ($user->getStripeCustomerId()) {
            return $user->getStripeCustomerId();
        }

        $customer = \Stripe\Customer::create([
            'email' => $user->getEmail(),
            'metadata' => [
                'user_id' => $user->getId(),
            ]
        ]);

        $user->setStripeCustomerId($customer->id);
        $this->em->persist($user);
        $this->em->flush();

        return $customer->id;
    }

    private function isUserPremium(UserInterface $user) {

        $customerId = $this->getStripeCustomerId($user);

        return \Stripe\Subscription::all([
            'customer' => $customerId,
            'price' => $this->premiumPriceId,
            'status' => 'active',
        ])->count() > 0;
    }


    public function cancelSubscription(UserInterface $user) {
        $customerId = $this->getStripeCustomerId($user);

        $subscription = \Stripe\Subscription::all([
            'customer' => $customerId,
            'price' => $this->premiumPriceId,
            'status' => 'active',
        ]);

        foreach ($subscription as $sub) {
            $sub->cancel();
        }

        $user->setRoles(array_filter($user->getRoles(), function ($role) {
            return $role !== User::ROLE_PREMIUM;
        }));
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @throws ApiErrorException
     * @throws \HttpException
     */
    public function createPremiumSubscription(UserInterface $user): array
    {

        $stripeCustomerId = $this->getStripeCustomerId($user);

        if ($this->isUserPremium($user)) {
           return [
               'premium' => "active",
           ];
        }


        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price' => $this->premiumPriceId,
                    'quantity' => 1,
                ],
            ],
            'mode' => 'subscription',
            'success_url' => $this->front_end_url . '/profile/' . $user->getPseudo() . '?premium_subscription=success',
            'cancel_url' => $this->front_end_url . '/profile/' . $user->getPseudo() . '?premium_subscription=cancelled',
            'client_reference_id' => $user->getId(),
            'customer' => $stripeCustomerId,
        ]);

        return [
            'premium' => 'link',
            'url' => $checkout_session->url,
        ];
    }

}

