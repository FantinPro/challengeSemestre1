<?php

namespace App\Tests;

use App\Entity\Message;
use App\Entity\Report;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;

class AdminReportsTest extends SetupTest
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetReportsAdmin(): void
    {
        $token = $this->login('admin@gmail.com', 'password');
        $client = static::createClient();
        $response = $client->request('GET', '/api/messages/reports', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testGetReportsModerator(): void
    {
        $token = $this->login('moderator@gmail.com', 'password');
        $client = static::createClient();
        $response = $client->request('GET', '/api/messages/reports', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testGetReportsBasicUser(): void
    {
        $token = $this->login('user@gmail.com', 'password');
        $client = static::createClient();
        $response = $client->request('GET', '/api/messages/reports', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCantReportHimSelf(): void
    {

        $messageRepo = static::getContainer()->get(MessageRepository::class);
        $userRepo = static::getContainer()->get(UserRepository::class);

        $user = $userRepo->findOneBy([
            'email' => 'user@gmail.com'
        ]);

        $message = $messageRepo->findOneBy([
            'creator' => $user
        ]);

        $token = $this->login('user@gmail.com', 'password');
        $client = static::createClient();
        $response = $client->request('POST', '/api/reports', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'reportedMessage' => '/api/messages/' . $message->getId(),
                'reportingUser' => '/api/users/' . $user->getId(),
                'type' => 'racism'
            ],
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCanReportOther(): void
    {

        $messageRepo = static::getContainer()->get(MessageRepository::class);
        $userRepo = static::getContainer()->get(UserRepository::class);

        $user = $userRepo->findOneBy([
            'email' => 'user@gmail.com'
        ]);

        $message = $messageRepo->findOneBy([
            'creator' => $user
        ]);

        $premium = $userRepo->findOneBy([
            'email' => 'premium@gmail.com'
        ]);

        $token = $this->login('premium@gmail.com', 'password');
        $client = static::createClient();
        $response = $client->request('POST', '/api/reports', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'reportedMessage' => '/api/messages/' . $message->getId(),
                'reportingUser' => '/api/users/' . $premium->getId(),
                'type' => 'racism'
            ],
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testSingleReportOnMessageDoesNotMakeItAppearInTheList(): void
    {
        $token = $this->login('moderator@gmail.com', 'password');
        $client = static::createClient();
        $response = $client->request('GET', '/api/messages/reports', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ],
        ]);
        $data = json_decode($response->getContent());
        $reportsCount = count($data);

        $userRepo = static::getContainer()->get(UserRepository::class);

        $premium = $userRepo->findOneBy([
            'email' => 'premium@gmail.com'
        ]);

        $user = $userRepo->findOneBy([
            'email' => 'user@gmail.com'
        ]);

        $messagePremium = (new Message())
            ->setContent('This is a test message by user premium')
            ->setCreator($premium);

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($messagePremium);

        $reportByUser = (new Report())
            ->setReportingUser($user)
            ->setReportedMessage($messagePremium)
            ->setType('racism');

        $em->persist($reportByUser);
        $em->flush();

        $response2 = $client->request('GET', '/api/messages/reports', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ],
        ]);
        $data2 = json_decode($response2->getContent());

        // same as before because the message has only one report
        $this->assertEquals($reportsCount, count($data2));
    }

    public function testTwoReportsOnMessageMakeItAppearInTheList(): void
    {
        $token = $this->login('moderator@gmail.com', 'password');
        $client = static::createClient();
        $response = $client->request('GET', '/api/messages/reports', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ],
        ]);
        $data = json_decode($response->getContent());
        $reportsCount = count($data);

        $userRepo = static::getContainer()->get(UserRepository::class);

        $premium = $userRepo->findOneBy([
            'email' => 'premium@gmail.com'
        ]);

        $moderator = $userRepo->findOneBy([
            'email' => 'moderator@gmail.com'
        ]);

        $user = $userRepo->findOneBy([
            'email' => 'user@gmail.com'
        ]);

        $messagePremium = (new Message())
            ->setContent('This is a test message by user premium')
            ->setCreator($premium);

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($messagePremium);

        $reportByUser = (new Report())
            ->setReportingUser($user)
            ->setReportedMessage($messagePremium)
            ->setType('racism');

        $reportByModerator = (new Report())
            ->setReportingUser($moderator)
            ->setReportedMessage($messagePremium)
            ->setType('racism');

        $em->persist($reportByUser);
        $em->persist($reportByModerator);
        $em->flush();

        $response2 = $client->request('GET', '/api/messages/reports', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ],
        ]);
        $data2 = json_decode($response2->getContent());

        // same as before +1 because the message has 2 reports
        $this->assertEquals($reportsCount + 1, count($data2));
    }


    public function testCanRejectReportsFromMessage(): void
    {
        $messageRepo = static::getContainer()->get(MessageRepository::class);
        $token = $this->login('moderator@gmail.com', 'password');
        $client = static::createClient();

        $messageReported = $messageRepo->findOneBy([
            'content' => 'This is a test message by user premium'
        ]);

        $response = $client->request('DELETE',
            '/api/messages/reports/reject?messageId=' . $messageReported->getId(), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json'
                ],
            ]);

        $this->assertResponseIsSuccessful();
    }

    public function testCanPatchDeleteMessage(): void
    {
        $messageRepo = static::getContainer()->get(MessageRepository::class);
        $token = $this->login('moderator@gmail.com', 'password');
        $client = static::createClient();

        $messageReported = $messageRepo->findOneBy([
            'content' => 'This is a test message by user premium'
        ]);

        $response = $client->request('PATCH',
            '/api/messages/' . $messageReported->getId(), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'isDeleted' => true
                ]
            ]);

        $this->assertResponseIsSuccessful();
    }


}
