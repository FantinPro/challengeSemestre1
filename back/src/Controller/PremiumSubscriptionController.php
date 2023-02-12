<?php

namespace App\Controller;

use App\Entity\Pub;
use App\Entity\User;
use App\Service\PaymentService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use Psr\Log\LoggerInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
class PremiumSubscriptionController extends AbstractController
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly EntityManagerInterface $em,

    )
    {
    }

    #[Route('/api/premium_subscription', name: 'get_subscription_link', methods: ['GET'])]
    public function createSubscriptionLink(): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Unauthorized', 401);
        }
        return new JsonResponse($this->paymentService->createPremiumSubscription($this->getUser()));
    }

    #[Route('/api/premium_subscription', name: 'delete_subscription', methods: ['DELETE'])]
    public function cancelSubscription(): Response
    {
        if (!$this->getUser()) {
            return new JsonResponse('Unauthorized', 401);
        }
        $this->paymentService->cancelSubscription($this->getUser());

        return new JsonResponse(['message' => 'Subscription cancelled'], 200);
    }

    #[Route('/api/webhooks', name: 'webhook_handler', methods: ['POST'])]
    public function webhookHandler(\Symfony\Component\HttpFoundation\Request $request) {
        $event = null;
        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($request->getContent(), true)
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            return new Response('Invalid payload', 400);
        }

        switch ($event->type) {
            case 'customer.subscription.trial_will_end':
                $subscription = $event->data->object; // contains a \Stripe\Subscription
                // Then define and call a method to handle the trial ending.
                // handleTrialWillEnd($subscription);
                break;
            case 'customer.subscription.created':
                $subscription = $event->data->object; // contains a \Stripe\Subscription
                // Then define and call a method to handle the subscription being created.
                // handleSubscriptionCreated($subscription);
                if ($subscription->status === 'active') {
                    $user = $this->em->getRepository(User::class)->findOneBy(['stripeCustomerId' => $subscription->customer]);

                    if ($user) {
                        $roles = $user->getRoles();
                        $roles[] = User::ROLE_PREMIUM;
                        $user->setRoles($roles);
                        $this->em->persist($user);
                        $this->em->flush();
                    }
                }
                break;
            case 'customer.subscription.deleted':
                $subscription = $event->data->object; // contains a \Stripe\Subscription
                // Then define and call a method to handle the subscription being deleted.
                // handleSubscriptionDeleted($subscription);
                break;
            case 'customer.subscription.updated':
                $subscription = $event->data->object; // contains a \Stripe\Subscription
                // Then define and call a method to handle the subscription being updated.
                    // handleSubscriptionUpdated($subscription);
                if ($subscription->status === 'active') {
                   $user = $this->em->getRepository(User::class)->findOneBy(['stripeCustomerId' => $subscription->customer]);



                     if ($user) {

                         $roles = $user->getRoles();
                         $roles[] = User::ROLE_PREMIUM;
                         $user->setRoles($roles);
                        $this->em->persist($user);
                        $this->em->flush();
                     }
                }
                break;
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                // Then define and call a method to handle the successful payment intent.
                // handlePaymentIntentSucceeded($paymentIntent);
                $checkouts = Session::all([
                    'payment_intent' => $paymentIntent->id,
                ])->data;

                if (!$checkouts) {
                    return new Response('Invalid payload', 400);
                }

                foreach ($checkouts as $checkout) {
                    $pub = $this->em->getRepository(Pub::class)->findOneBy(['paymentIntentId' => $checkout->id]);
                    if ($pub) {
                        $pub->setStatus(Pub::STATUS_PAYED);
                        $this->em->persist($pub);
                        $this->em->flush();
                    }
                }

                break;
            default:
                // Unexpected event type
                echo 'Received unknown event type';
        }
        return new Response('Webhook received', 200);
    }
}