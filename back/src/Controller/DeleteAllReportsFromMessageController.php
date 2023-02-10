<?php

namespace App\Controller;


use App\Entity\Message;
use App\Entity\UserToUser;
use App\Repository\MessageRepository;
use App\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class DeleteAllReportsFromMessageController extends AbstractController
{

    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly ReportRepository $reportRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }
    public function __invoke(Request $request)
    {

        $messageId = $request->query->get('messageId');
        $message = $this->messageRepository->find($messageId);

        if (!$message) {
            return new JsonResponse(['error' => 'Message not found'], 404);
        }

        // delete all reports that belong to this message
        $this->reportRepository->deleteAllReportsFromMessage($message->getId());
        // update report
        $message->setIsDeleted(false);
        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }
}
