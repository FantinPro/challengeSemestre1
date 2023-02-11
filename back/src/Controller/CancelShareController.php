<?php

namespace App\Controller;


use App\Entity\Message;
use App\Entity\UserToUser;
use App\Repository\MessageRepository;
use App\Repository\ReportRepository;
use App\Repository\ShareRepository;
use App\Repository\UserToUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class CancelShareController extends AbstractController
{

    public function __construct(
        private readonly ShareRepository $shareRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }
    public function __invoke(Request $request)
    {

        $messageId = $request->query->get('messageId', null);

        if (!$messageId) {
            return new JsonResponse(['error' => 'Message not found'], 404);
        }

        $user = $this->getUser();

        $shareToDelete = $this->shareRepository->findOneBy([
            'sharedMessage' => $messageId,
            'sharingBy' => $user->getId()
        ]);

        if(!$shareToDelete) {
            return new JsonResponse(['error' => 'Relation not found'], 404);
        }

        $this->entityManager->remove($shareToDelete);
        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }
}
