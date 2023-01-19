<?php

namespace App\Controller;


use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class MessageWithAtLeast2Reports extends AbstractController
{
    public function construct(private readonly MessageRepository $messageRepository)
    {
    }
    public function invoke(Request $request): array
    {

        return $this->messageRepository->findWithAtLeast2Reports();

    }
}