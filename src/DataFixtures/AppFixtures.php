<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Like;
use App\Entity\Note;
use App\Entity\User;
use App\Entity\Network;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $slug = null;
    private $hash = null;

    public function __construct(
        private SluggerInterface $slugger,
        private UserPasswordHasherInterface $hasher
    ) {
        $this->slug = $slugger;
        $this->hash = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création de catégories
        $categories = [
            'HTML' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-plain.svg',
            'CSS' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-plain.svg',
            'JavaScript' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-plain.svg',
            'PHP' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-plain.svg',
            'SQL' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/postgresql/postgresql-plain.svg',
            'JSON' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/json/json-plain.svg',
            'Python' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/python/python-plain.svg',
            'Ruby' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/ruby/ruby-plain.svg',
            'C++' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/cplusplus/cplusplus-plain.svg',
            'Go' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/go/go-original-wordmark.svg',
            'bash' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/bash/bash-plain.svg',
            'Markdown' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/markdown/markdown-original.svg',
            'Java' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/java/java-original-wordmark.svg',
        ];

        $categoryArray = []; // Ce tableau nous servira pour conserver les objets Category

        foreach ($categories as $title => $icon) {
            $category = new Category(); // Nouvel objet Category
            $category
                ->setTitle($title) // Ajoute le titre
                ->setIcon($icon) // Ajoute l'icone
            ;

            array_push($categoryArray, $category); // Ajout de l'objet
            $manager->persist($category);
        }

        // 1 admin
        $admin = new User();
        $admin
            ->setEmail('hello@codexpress.fr')
            ->setUsername('Jensone')
            ->setPassword($this->hash->hashPassword($admin, 'admin'))
            ->setRoles(['ROLE_ADMIN'])
            ;
        $manager->persist($admin);

        // 10 utilisateurs + 10 notes chacun + 3 network
        $users = [];
        $notes = [];
        $networks = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'github', 'reddit', 'discord', 'telegram'];
        for ($i = 0; $i < 10; $i++) {
            $username = $faker->userName; // Génére un username aléatoire
            $usernameFinal = $this->slug->slug($username); // Username en slug
            $user =  new User();
            $user
                ->setEmail($usernameFinal . '@' . $faker->freeEmailDomain)
                ->setUsername($username)
                ->setPassword($this->hash->hashPassword($user, 'admin'))
                ->setRoles(['ROLE_USER'])
                ;
            array_push($users, $user);
            $manager->persist($user);

            // Ajoute 3 réseaux
            for ($k=0; $k < 3; $k++) {
                $networkSelected = $faker->randomElement($networks);
                $network = new Network();
                $network
                    ->setCreator($user)
                    ->setName($networkSelected)
                    ->setUrl('https://' . strtolower($networkSelected) . '.com/' . $user->getUsername())
                    ;
                $manager->persist($network);
            }

            for ($j=0; $j < 10; $j++) { 
                $note = new Note();
                $note
                    ->setTitle($faker->words(5, true))
                    ->setSlug($this->slug->slug($note->getTitle()))
                    ->setContent($faker->randomHtml)
                    ->setPublic($faker->boolean(50))
                    ->setViews($faker->numberBetween(100, 10000))
                    ->setCreator($user)
                    ->setCategory($faker->randomElement($categoryArray))
                    ;
                array_push($notes, $note);
                $manager->persist($note);
            }
        }

        $manager->flush();

        // 100 nouveaux utilisateurs sans notes
        for ($i = 0; $i < 100; $i++) {
            $username = $faker->userName; // Génére un username aléatoire
            $usernameFinal = $this->slug->slug($username); // Username en slug
            $user =  new User();
            $user
                ->setEmail($usernameFinal . '@' . $faker->freeEmailDomain)
                ->setUsername($username)
                ->setPassword($this->hash->hashPassword($user, 'admin'))
                ->setRoles(['ROLE_USER'])
                ;
            
            // 10 likes aléatoires
            for ($j=0; $j < 10; $j++) {
                $noteSelected = $faker->randomElement($notes);
                $user = $faker->randomElement($users);
                if ($user->getId() != $noteSelected->getCreator()->getId()) {
                    $like = new Like();
                    $like
                        ->setNote($noteSelected)
                        ->setCreator($user)
                        ;
                    $manager->persist($like);
                }
            }
            $manager->persist($user);
        }

        $manager->flush();
    }
}
