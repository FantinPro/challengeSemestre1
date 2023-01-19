<?php

namespace App\Controller;


use App\Entity\Message;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class FeedV2Controller extends AbstractController
{
    const PAGE_SIZE = 100;

    public function __construct(private readonly MessageRepository $messageRepository)
    {
    }
    public function __invoke(Request $request): array
    {

        $page = $request->query->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $user = $this->getUser();

        return $this->messageRepository->getFeed($user->getId(), $page, self::PAGE_SIZE);

    }
}
