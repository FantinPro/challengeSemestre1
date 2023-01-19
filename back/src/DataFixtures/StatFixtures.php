<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Message;
use App\Entity\Stat;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StatFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        $users = $manager->getRepository(User::class)->findAll();

        $adThatStartNow = $this->getReference('adThatStartNow');

        foreach ($users as $user) {
            $stat = (new Stat())
                ->setFromUser($user)
                ->setAd($adThatStartNow)
                ->setClick(boolval($faker->numberBetween(0, 1)));

            $manager->persist($stat);
        }

        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            AdFixtures::class,
        ];
    }
}



