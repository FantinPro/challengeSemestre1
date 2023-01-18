<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        $admin = $manager->getRepository(User::class)->findOneBy([
            'email' => 'admin@gmail.com'
        ]);
        $user = $manager->getRepository(User::class)->findOneBy([
            'email' => 'user@gmail.com'
        ]);

        for ($i = 0; $i < 3; $i++) {
            $message = (new Message())
                ->setCreator($admin)
                ->setContent($faker->paragraph(2));

            for ($y = 0; $y < 10; $y++) {
                $comment = (new Message())
                    ->setCreator($user)
                    ->setContent($faker->paragraph(2));
                $manager->persist($comment);
                $message->addComment($comment);
            }

            $manager->persist($message);

        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}



