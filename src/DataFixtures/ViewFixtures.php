<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\View;
use App\DataFixtures\NoteFixtures;
use App\Repository\NoteRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ViewFixtures extends Fixture implements DependentFixtureInterface
{
    private $notes = null;

    public function __construct(
        private readonly NoteRepository $nr
    ) {
        $this->notes = $nr->findBy(['is_public' => true]);
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 1000; $i++) {
            $view = new View();
            $view
                ->setNote($faker->randomElement($this->notes))
                ->setIpAddress($faker->ipv4)
            ;
            $manager->persist($view);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [NoteFixtures::class];
    }
}
