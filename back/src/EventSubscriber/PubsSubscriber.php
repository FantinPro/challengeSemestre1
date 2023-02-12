<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Pub;
use App\Service\PaymentService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PubsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private PaymentService $paymentService
    ) {
        \Stripe\Stripe::setApiKey($_SERVER['STRIPE_PK']);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['sendPaymentIntent', EventPriorities::POST_WRITE],
            ]
        ];
    }

    public function sendPaymentIntent(ViewEvent $event): void
    {
        $pub = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $body = json_decode($event->getRequest()->getContent());

        if (!$pub instanceof Pub || !in_array($method, [Request::METHOD_PATCH, Request::METHOD_PUT])) {
            return;
        }

        if (isset($body->status)) {
            if($body->status === 'accepted') {
                $this->paymentService->createAdPaymentLink($pub);
            }
        }
    }
}