<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserToUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserToUserFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {

        $admin = $manager->getRepository(User::class)->findOneBy([
            'email' => 'admin@gmail.com'
        ]);
        $users = $manager->getRepository(User::class)->findAll();

        // filter admin@gmail.com
        $users = array_filter($users, function ($user) {
            return $user->getEmail() !== 'admin@gmail.com';
        });

        foreach ($users as $user) {
            $userToUser = (new UserToUser())
                ->setMe($admin)
                ->setOther($user)
                ->setStatus('following')
            ;
            $manager->persist($userToUser);
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
