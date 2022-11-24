<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use App\Entity\Movie;
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
        $users = $this->createUsers($manager);
        $this->createMovies($manager, $users);

        $manager->flush();
    }

    private function createUsers(ObjectManager $manager)
    {
        $users = [];
        $users[] = $user = (new User())
            ->setEmail('john.doe@example.org')
            ->setBirthday(new \DateTimeImmutable('22-05-1987'))
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
        ;
        $user->setPassword($this->hasher->hashPassword($user, 'admin1234'));
        $manager->persist($user);

        $users[] = $user = (new User())
            ->setEmail('jane.doe@example.org')
            ->setBirthday(new \DateTimeImmutable('05-05-1987'))
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
        ;
        $user->setPassword($this->hasher->hashPassword($user, 'admin1234'));
        $manager->persist($user);

        return $users;
    }

    private function createMovies(ObjectManager $manager, array $users)
    {
        $movie = (new Movie())
            ->setTitle('Star Wars: Episode IV - A New Hope')
            ->setPoster('https://m.media-amazon.com/images/M/MV5BOTA5NjhiOTAtZWM0ZC00MWNhLThiMzEtZDFkOTk2OTU1ZDJkXkEyXkFqcGdeQXVyMTA4NDI1NTQx._V1_SX300.jpg')
            ->setCountry('United States')
            ->setReleasedAt(new \DateTimeImmutable('25 May 1977'))
            ->setRated('PG')
            ->setImdbId('tt0076759')
            ->setPrice('5.0')
            ->addGenre((new Genre())->setName('Action'))
            ->addGenre((new Genre())->setName('Adventure'))
            ->addGenre((new Genre())->setName('Fantasy'))
            ->setCreatedBy($users[0])
            ;
        $manager->persist($movie);
    }
}
