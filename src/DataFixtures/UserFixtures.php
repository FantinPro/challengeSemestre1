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
        $teacher = (new User())
            ->setEmail('admin@gmail.com')
            ->setIsVerified(true)
            ->setRoles(['ROLE_ADMIN'])
        ;
        $teacher->setPassword($this->userPasswordHasher->hashPassword($teacher, 'password'));
        $manager->persist($teacher);

        $user = (new User())
            ->setEmail('user@gmail.com')
            ->setIsVerified(true)
            ->setRoles(['ROLE_USER'])
        ;
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

        $notVerfiedUser = (new User())
            ->setEmail('notverified@gmail.com')
            ->setIsVerified(false)
            ->setRoles(['ROLE_USER'])
        ;
        $notVerfiedUser->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $manager->persist($notVerfiedUser);

        $manager->flush();
    }
}
