<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\Share;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ShareFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $messageFromAdmin = $this->getReference('messageFromAdmin');
            $share  = (new Share())
                ->setSharingBy($user)
                ->setSharedMessage($messageFromAdmin);
            $manager->persist($share);
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



