<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\Share;
use App\Entity\User;
use App\Entity\UserToUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ShareFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $admin = $manager->getRepository(User::class)->findOneBy([
            'email' => 'admin@gmail.com'
        ]);

        $follows = $admin->getFollows()->map(fn (UserToUser $follow) => $follow->getOther());

        $messageFromAdmin = $this->getReference('messageFromAdmin');
        $messageFromNobody = $this->getReference('nobodyFollowUserMessage');

        foreach ($follows as $user) {

            $share  = (new Share())
                ->setSharingBy($user)
                ->setSharedMessage($messageFromAdmin);
            $manager->persist($share);


            $share2 = (new Share())
                ->setSharingBy($user)
                ->setSharedMessage($messageFromNobody);
            $manager->persist($share2);
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



