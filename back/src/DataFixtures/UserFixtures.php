<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    /** @var UserPasswordHasherInterface $userPasswordHasher */
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        $admin = (new User())
            ->setEmail('admin@gmail.com')
            ->setIsVerified(true)
            ->setRoles(['ROLE_ADMIN'])
            ->setBio('I am the admin be nice to me')
            ->setPseudo('admin')
        ;
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        $user = (new User())
            ->setEmail('user@gmail.com')
            ->setIsVerified(true)
            ->setRoles(['ROLE_USER'])
            ->setPseudo('defaultUser')
        ;
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

        $moderator = (new User())
            ->setEmail('moderator@gmail.com')
            ->setIsVerified(true)
            ->setRoles(['ROLE_MODERATOR'])
            ->setPseudo('modo')
        ;
        $moderator->setPassword($this->userPasswordHasher->hashPassword($moderator, 'password'));
        $manager->persist($moderator);

        $notVerified = (new User())
            ->setEmail('notverified@gmail.com')
            ->setIsVerified(false)
            ->setRoles(['ROLE_USER'])
            ->setPseudo('not_verified')
        ;
        $notVerified->setPassword($this->userPasswordHasher->hashPassword($notVerified, 'password'));
        $manager->persist($notVerified);

        $premiumUser = (new User())
            ->setEmail('premium@gmail.com')
            ->setIsVerified(true)
            ->setRoles(['ROLE_PREMIUM'])
            ->setPseudo('premium')
        ;
        $premiumUser->setPassword($this->userPasswordHasher->hashPassword($premiumUser, 'password'));
        $manager->persist($premiumUser);

        $premiumUser2 = (new User())
            ->setEmail('premium2@gmail.com')
            ->setIsVerified(true)
            ->setRoles(['ROLE_PREMIUM'])
            ->setPseudo('premium2')
        ;
        $premiumUser2->setPassword($this->userPasswordHasher->hashPassword($premiumUser2, 'password'));
        $manager->persist($premiumUser2);

        $nobodyFollowMe = (new User())
            ->setEmail('nobodyfollowme@gmail.com')
            ->setIsVerified(true)
            ->setRoles(['ROLE_USER'])
            ->setPseudo('nobodyfollowme')
        ;
        $nobodyFollowMe->setPassword($this->userPasswordHasher->hashPassword($nobodyFollowMe, 'password'));
        $manager->persist($nobodyFollowMe);


        // bunch of 30 random user with random pseudo
        for ($i = 0; $i < 30; $i++) {
            $user = (new User())
                ->setEmail($faker->email())
                ->setIsVerified(true)
                ->setRoles(['ROLE_USER'])
                ->setPseudo($faker->userName())
            ;
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
