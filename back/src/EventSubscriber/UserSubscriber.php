<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;
use App\Service\UserEmailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private UserEmailService $userEmailService,
        private UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                ['sendEmailConfirmation', EventPriorities::POST_WRITE],
                ['hashPassword', EventPriorities::PRE_WRITE],
            ],
        ];
    }

    public function sendEmailConfirmation(ViewEvent $event): void
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$user instanceof User || Request::METHOD_POST !== $method) {
            return;
        }

        $this->userEmailService->sendEmailVerification($user);
    }

    public function hashPassword(ViewEvent $event): void
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$user instanceof User || !in_array($method, [Request::METHOD_POST, Request::METHOD_PATCH, Request::METHOD_PUT])) {
            return;
        }

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
    }
}
