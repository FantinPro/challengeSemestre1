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
        $admin = (new User())
            ->setEmail('admin@gmail.com')
            ->setIsVerified(true)
            ->setRoles(['ROLE_ADMIN'])
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

        $notVerfiedUser = (new User())
            ->setEmail('notverified@gmail.com')
            ->setIsVerified(false)
            ->setRoles(['ROLE_USER'])
            ->setPseudo('notverified')
        ;
        $notVerfiedUser->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $manager->persist($notVerfiedUser);

        $manager->flush();
    }
}
