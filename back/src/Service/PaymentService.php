<?php

namespace App\Service;

use App\Entity\Pub;
use App\Entity\User;
use Doctrine\DBAL\Driver\OCI8\Exception\Error;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Subscription;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

class PaymentService
{
    public function __construct(private string $stripePK, private string $premiumPriceId, private string $front_end_url, private readonly EntityManagerInterface $em, private readonly UserEmailService $userEmailService,private LoggerInterface $logger)
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


    public function createAdPaymentLink(Pub $pub)
    {
        $customerId = $this->getStripeCustomerId($pub->getOwner());

        $session = \Stripe\Checkout\Session::create([
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'product' => 'prod_NL94KpnefCl8zu',
                    'unit_amount' => $pub->getPrice() * 100,
                    'currency' => 'eur',
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'expires_at' => time() + 60 * 60 * 24,
            'success_url' => $this->front_end_url . '/dashboard/manage_ads?ad_payment=success',
            'cancel_url' => $this->front_end_url . '/dashboard/manage_ads?ad_payment=cancel',
        ]);

        $pub->setPaymentIntentId($session->id);
        $this->em->persist($pub);
        $this->em->flush();

        $this->userEmailService->sendAdPaymentLink($pub->getOwner(), $session->url);
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

