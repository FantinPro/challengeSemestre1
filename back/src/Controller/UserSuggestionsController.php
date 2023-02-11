<?php

namespace App\Controller;


use App\Entity\Message;
use App\Entity\UserToUser;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Repository\UserToUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class UserSuggestionsController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
    )
    {
    }
    public function __invoke(Request $request)
    {

        $user = $this->getUser();

        $follows = $user->getFollows()->map(fn(UserToUser $follow) => $follow->getOther()->getId())->toArray();

        $suggestions = $this->userRepository->findSuggestionsNotIn([$user->getId(), ...$follows]);

        return $suggestions;

    }
}
