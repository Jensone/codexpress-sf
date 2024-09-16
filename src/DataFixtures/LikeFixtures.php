<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Like;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LikeFixtures extends Fixture implements DependentFixtureInterface
{
    private $users = null;
    private $notes = null;

    public function __construct(
        private readonly UserRepository $ur,
        private readonly NoteRepository $nr
    ) {
        $this->users = $ur->findAll();
        $this->notes = $nr->findBy(['is_public' => true]);
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        foreach ($this->users as $user) {
            for ($i = 0; $i < 5; $i++) {
                $like = new Like();
                $like
                    ->setNote($faker->randomElement($this->notes))
                    ->setCreator($user)
                ;
                $manager->persist($like);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [NoteFixtures::class, UserFixtures::class];
    }
}
