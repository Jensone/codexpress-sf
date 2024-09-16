<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Note;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class NoteFixtures extends Fixture implements DependentFixtureInterface
{
    private $users = null;
    private $slug = null;
    private $categories = null;

    public function __construct(
        private readonly UserRepository $ur,
        private readonly SluggerInterface $slugger,
        private readonly CategoryRepository $cr
    ) {
        $this->users = $ur->findBy(['roles' => 'ROLE_USER']);
        $this->slug = $slugger;
        $this->categories = $cr->findAll();
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 500; $i++) {
            $note = new Note();
            $note
                ->setTitle($faker->words(5, true))
                ->setSlug($this->slug->slug($note->getTitle()))
                ->setContent($faker->randomHtml)
                ->setPublic($faker->boolean(70))
                ->setCreator($faker->randomElement($this->users))
                ->setCategory($faker->randomElement($this->categories))
            ;
            $manager->persist($note);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class, CategoryFixtures::class];
    }
}
