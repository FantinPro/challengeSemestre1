<?php

namespace App\Controller;


use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class MessageWithAtLeast2Reports extends AbstractController
{
    public function __construct(private readonly MessageRepository $messageRepository)
    {
    }
    public function __invoke(Request $request): array
    {

        return $this->messageRepository->findWithAtLeast2Reports();
        
    }
}
