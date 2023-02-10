<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\Security\Core\User\UserInterface;

class PaymentService
{
    private \Stripe\StripeClient $stripe;

    public function __construct(private string $stripePK, private string $premiumPriceId,private readonly EntityManagerInterface $em)
    {
        $this->stripe = new \Stripe\StripeClient($stripePK);
    }


    private function createCustomerIdIfNotExists(UserInterface $user): bool
    {
        if ($user->getStripeCustomerId() === null) {

                $customer = $this->stripe->customers->create([
                    'email' => $user->getEmail(),
                ]);
                $user->setStripeCustomerId($customer->id);
                $this->em->persist($user);
                $this->em->flush();
                return true;



        }
        return true;
    }

    /**
     * @throws Exception
     */
    private function hasPremiumSubscription(UserInterface $user): array | false
    {
        $this->createCustomerIdIfNotExists($user);
        $subscriptions = $this->stripe->subscriptions->all([
            'customer' => $user->getStripeCustomerId(),
            'price' => $this->premiumPriceId,
        ]);
        foreach ($subscriptions->data as $subscription) {
            if ($subscription->items->data[0]->price->id === $this->premiumPriceId) {
                // return the subscription id and the private key
                // get the latest invoice
                $invoice = $this->stripe->invoices->retrieve($subscription->latest_invoice);
                $paymentIntent = $this->stripe->paymentIntents->retrieve($invoice->payment_intent);

                return ['subscriptionId' => $subscription->id, 'privateKey' => $paymentIntent->client_secret];
            }
        }
        return false;

    }

    function deletePremiumSubscription(UserInterface $user): bool
    {
        $this->createCustomerIdIfNotExists($user);
        $subscriptions = $this->stripe->subscriptions->all([
            'customer' => $user->getStripeCustomerId(),
            'price' => $this->premiumPriceId,
        ]);
        foreach ($subscriptions->data as $subscription) {
            if ($subscription->items->data[0]->price->id === $this->premiumPriceId) {
                $this->stripe->subscriptions->cancel($subscription->id);
                return true;
            }
        }
        return false;

    }

    /**
     * @throws ApiErrorException
     * @throws Exception
     */
    public function createPremiumSubscription(UserInterface $user): array
    {
        $premium = $this->hasPremiumSubscription($user);
        if ($premium) {
            return $premium;
        }

        $sub = $this->stripe->subscriptions->create([
            'customer' => $user->getStripeCustomerId(),
            'items' => [
                [
                    'price' => $this->premiumPriceId,
                ],
            ],
            'expand' => ['latest_invoice.payment_intent'],
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
        ]);

        return ["subscriptionId" => $sub->id, "clientSecret" => $sub->latest_invoice->payment_intent->client_secret];
    }

}

