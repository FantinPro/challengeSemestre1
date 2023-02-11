<?php

namespace App\Controller;


use App\Entity\Message;
use App\Entity\UserToUser;
use App\Repository\MessageRepository;
use App\Repository\ShareRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class FeedV2Controller extends AbstractController
{
    const PAGE_SIZE = 25;

    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly ShareRepository $shareRepository,
    )
    {
    }
    public function __invoke(Request $request)
    {

        $page = $request->query->get('page', 1);
        if (!$page || $page < 1) {
            $page = 1;
        }

        $user = $this->getUser();

        $follows = $user->getFollows()->map(fn (UserToUser $follow) => $follow->getOther()->getId())->toArray();

        $messages = $this->messageRepository->getFeed($user->getId(), self::PAGE_SIZE, $page);

        foreach ($messages as $message) {

            $share = $this->shareRepository->findOneBy([
                'sharedMessage' => $message->getId(),
                'sharingBy' => $user->getId()
            ]);

            if ($share) {
                $message->setShared(true);
            }

            // $message->setWhoHasSharedFromMyFollows($follows);
        }

        return $messages;
    }
}
