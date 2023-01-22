<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;

class SetupTest extends ApiTestCase
{

    protected static $wasSetup = false;
    protected static $application;

    public function setUp(): void
    {
        if (!static::$wasSetup) {
            echo 'Setting up database Fixtures !!!' . PHP_EOL;
            self::runCommand('doctrine:fixtures:load --no-interaction');
            static::$wasSetup = true;
        }

    }

    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    public function login($email, $password): string
    {
        $client = static::createClient();
        $response = $client->request('POST', '/auth', [
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent());
        return $data->token;
    }

    public function testDisableWarning()
    {
        $this->assertTrue(true);
    }
}
