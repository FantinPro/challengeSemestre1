<?php

namespace App\Controller;


use App\Entity\Message;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class FeedController extends AbstractController
{
    const PAGE_SIZE = 10;

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

        $follows = $user->getFollows()->map(fn($follow) => $follow->getOther()->getId());
        return $this->messageRepository->findBy(array('creator' => $follows->toArray()),null,self::PAGE_SIZE, ($page - 1) * self::PAGE_SIZE);
    }
}