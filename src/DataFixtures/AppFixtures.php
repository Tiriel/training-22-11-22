<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createUsers($manager);

        $manager->flush();
    }

    private function createUsers(ObjectManager $manager)
    {
        $user = (new User())
            ->setEmail('john.doe@example.org')
            ->setBirthday(new \DateTimeImmutable('22-05-1987'))
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
        ;
        $user->setPassword($this->hasher->hashPassword($user, 'admin1234'));
        $manager->persist($user);

        $user = (new User())
            ->setEmail('jane.doe@example.org')
            ->setBirthday(new \DateTimeImmutable('05-05-1987'))
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
        ;
        $user->setPassword($this->hasher->hashPassword($user, 'admin1234'));
        $manager->persist($user);
    }
}
