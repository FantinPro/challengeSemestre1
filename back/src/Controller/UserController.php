<?php

namespace App\Controller;

use App\Entity\TokenResetPassword;
use App\Entity\User;
use App\Repository\TokenResetPasswordRepository;
use App\Repository\UserRepository;
use App\Service\UserEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

#[AsController]
class UserController extends AbstractController
{

    public function __construct(
        private UserEmailService $userEmailService,
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private TokenResetPasswordRepository $tokenResetPasswordRepository,
        private UserPasswordHasherInterface $userPasswordHasher
    )
    {}

    #[Route('/verify_email', name: 'app_verify_email', methods: ['GET'])]
    public function verifyEmail(Request $request) {
        $id = $request->get('id');

        $user = $this->userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $this->userEmailService->handleEmailConfirmation($request, $user);

        return new JsonResponse(['message' => 'User verified'], Response::HTTP_OK);
    }

    #[Route('/forgot_password', name: 'app_forgot_password', methods: ['POST'])]
    public function forgotPassword(Request $request) {
        // get email from Body
        $body = json_decode($request->getContent(), true);
        $email = $body['email'];

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $uuid = Uuid::uuid4();
        $dateExpireIn15Minutes = new \DateTime();
        $dateExpireIn15Minutes->modify('+15 minutes');
        $token = (new TokenResetPassword())
            ->setFromUser($user)
            ->setToken($uuid)
            ->setExpireAt($dateExpireIn15Minutes);

        $this->em->persist($token);
        $this->em->flush();

        $frontEndUrl = $this->getParameter('front_end_url');

        $this->userEmailService->sendForgotPasswordEmail($user, $token, $frontEndUrl);


        return new JsonResponse(['message' => 'Email sent'], Response::HTTP_OK);
    }

    #[Route('/reset_password', name: 'app_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request) {
        $body = json_decode($request->getContent(), true);
        $password = $body['password'];
        $token = $request->get('token');

        $tokenResetPassword = $this->tokenResetPasswordRepository->findOneBy(['token' => $token]);

        // check expiration (15 minutes) token
        if (!$tokenResetPassword || $tokenResetPassword->getExpireAt() < new \DateTime()) {
            return new JsonResponse(['message' => 'Token not found or expired'], Response::HTTP_NOT_FOUND);
        }

        $user = $tokenResetPassword->getFromUser();

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse(['message' => 'Password reset'], Response::HTTP_OK);
    }
}
