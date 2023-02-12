<?php

namespace App\Service;

use App\Entity\TokenResetPassword;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
use Symfony\Component\HttpFoundation\Request;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class UserEmailService
{

    private TransactionalEmailsApi $apiInstance;

    public function __construct(
        private string $env,
        private VerifyEmailHelperInterface $helper,
        private EntityManagerInterface $em
    )
    {
        $config =  Configuration::getDefaultConfiguration()->setApiKey('api-key', $env);
        $apiInstance = new TransactionalEmailsApi(
            new Client(),
            $config
        );
        $this->apiInstance = $apiInstance;
    }

    public function sendEmailVerification(User $user)
    {
        $signatureComponents = $this->helper->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );
        $link = $signatureComponents->getSignedUrl();

        $sendEmail = new SendSmtpEmail();
        $sendEmail['to'] = [["email" => $user->getEmail(), "name" => 'test']];
        $sendEmail['templateId'] = 5;
        $sendEmail['params'] = ['link' => $link];

        try {
            $response = $this->apiInstance->sendTransacEmail($sendEmail);
        } catch (\Exception $e) {
            echo 'Exception when calling AccountApi->getAccount: ', $e->getMessage(), PHP_EOL;
        }
    }

    public function sendAdPaymentLink(User $user, string $paymentLink) {
        $sendEmail = new SendSmtpEmail();
        $sendEmail['to'] = [["email" => $user->getEmail(), "name" => 'test']];
        $sendEmail['templateId'] = 11;
        $sendEmail['params'] = ['payment_link' => $paymentLink];

        try {
            $response = $this->apiInstance->sendTransacEmail($sendEmail);
        } catch (\Exception $e) {
            echo 'Exception when calling AccountApi->getAccount: ', $e->getMessage(), PHP_EOL;
        }
    }


    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, User $user): void
    {
        $this->helper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());

        $user->setIsVerified(true);

        $this->em->persist($user);
        $this->em->flush();
    }

    public function sendForgotPasswordEmail(User $user, TokenResetPassword $tokenResetPassword, string $frontEndUrl)
    {

        $link = "$frontEndUrl/reset_password?token={$tokenResetPassword->getToken()}";

        $sendEmail = new SendSmtpEmail();
        $sendEmail['to'] = [["email" => $user->getEmail(), "name" => 'test']];
        $sendEmail['templateId'] = 10;
        $sendEmail['params'] = ['link' => $link];

        try {
            $response = $this->apiInstance->sendTransacEmail($sendEmail);
        } catch (\Exception $e) {
            echo 'Exception when calling AccountApi->getAccount: ', $e->getMessage(), PHP_EOL;
        }
    }


}
