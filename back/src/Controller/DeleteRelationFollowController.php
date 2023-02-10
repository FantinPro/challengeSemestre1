<?php

namespace App\Controller;


use App\Entity\Message;
use App\Entity\UserToUser;
use App\Repository\MessageRepository;
use App\Repository\ReportRepository;
use App\Repository\UserToUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class DeleteRelationFollowController extends AbstractController
{

    public function __construct(
        private readonly UserToUserRepository $userToUserRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }
    public function __invoke(Request $request)
    {

        $userId = $request->query->get('userId', null);

        if (!$userId) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $user = $this->getUser();

        $relationToDelete = $this->userToUserRepository->findOneBy(['me' => $user->getId(), 'other' => $userId]);

        if(!$relationToDelete) {
            return new JsonResponse(['error' => 'Relation not found'], 404);
        }

        $this->entityManager->remove($relationToDelete);
        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }
}
