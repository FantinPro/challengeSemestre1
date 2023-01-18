<?php

namespace App\Controller;


use App\Entity\Message;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class FeedController extends AbstractController
{
    const PAGE_SIZE = 10;

    public function __construct(private readonly MessageRepository $messageRepository)
    {
    }
    public function __invoke(Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {

        $page = $request->query->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $user = $this->getUser();
        $follows = $user->getFollows()->getOther();
        return $this->json($this->messageRepository->findBy(['creator' => $follows, ['createdAt' => 'DESC', 'limit'=> self::PAGE_SIZE, 'offset' => ($page - 1) * self::PAGE_SIZE]]));
    }
}