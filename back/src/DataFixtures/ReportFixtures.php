<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\Report;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReportFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        $admin = $manager->getRepository(User::class)->findOneBy([
            'email' => 'admin@gmail.com'
        ]);
        $moderator = $manager->getRepository(User::class)->findOneBy([
            'email' => 'moderator@gmail.com'
        ]);
        $user = $manager->getRepository(User::class)->findOneBy([
            'email' => 'user@gmail.com'
        ]);

        $messages = $manager->getRepository(Message::class)->findBy([
            'creator' => $user
        ]);

        foreach ($messages as $message) {
            $reportByModerator = (new Report())
                ->setReportingUser($moderator)
                ->setReportedMessage($message)
                ->setType(Report::REPORT_TYPES[$faker->numberBetween(0, 2)]);

            $reportByAdmin = (new Report())
                ->setReportingUser($admin)
                ->setReportedMessage($message)
                ->setType(Report::REPORT_TYPES[$faker->numberBetween(0, 2)]);

            $manager->persist($reportByModerator);
            $manager->persist($reportByAdmin);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            MessageFixtures::class,
            UserToUserFixtures::class
        ];
    }
}



