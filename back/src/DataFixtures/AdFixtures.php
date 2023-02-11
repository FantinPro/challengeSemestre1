<?php

namespace App\DataFixtures;

use App\Entity\Pub;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AdFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        $premium = $manager->getRepository(User::class)->findOneBy([
            'email' => 'premium@gmail.com'
        ]);

        $premium2 = $manager->getRepository(User::class)->findOneBy([
            'email' => 'premium2@gmail.com'
        ]);

        $startTime = new \DateTime();
        $startTime->sub(new \DateInterval('P2D'));

        $endTime = new \DateTime();
        $endTime->add(new \DateInterval('P2D'));

        $adThatStartNow = (new Pub())
            ->setOwner($premium)
            ->setMessage($faker->paragraph(2))
            ->setPrice($faker->numberBetween(100, 1000))
            ->setStartDate($startTime)
            ->setEndDate($endTime)
            ->setStatus(Pub::STATUS_ACCEPTED);

        // use reference
        $this->addReference('adThatStartNow', $adThatStartNow);

        $startTime2 = new \DateTime();
        $startTime2->sub(new \DateInterval('P10D'));

        $endTime2 = new \DateTime();
        $endTime2->add(new \DateInterval('P20D'));

        $adThatStartIn10Days = (new Pub())
            ->setOwner($premium2)
            ->setMessage($faker->paragraph(2))
            ->setPrice($faker->numberBetween(100, 1000))
            ->setStartDate($startTime2)
            ->setEndDate($endTime2)
            ->setStatus(Pub::STATUS_ACCEPTED);

        $manager->persist($adThatStartNow);
        $manager->persist($adThatStartIn10Days);
        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}



