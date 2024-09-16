<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Network;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
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

        $networks = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'github', 'reddit', 'discord', 'telegram'];

        // 1 admin
        $admin = new User();
        $admin
            ->setEmail('hello@codexpress.fr')
            ->setUsername('Jensone')
            ->setPassword($this->hash->hashPassword($admin, 'admin'))
            ->setRoles(['ROLE_ADMIN'])
        ;
        for ($i = 0; $i < 3; $i++) {
            $network = new Network();
            $network
                ->setCreator($admin)
                ->setName($faker->randomElement($networks))
                ->setUrl('https://' . strtolower($faker->randomElement($networks)) . '.com/' . $admin->getUsername())
            ;
            $manager->persist($network);
        }
        $manager->persist($admin);

        // 10 créateurs premium
        for ($i = 0; $i < 10; $i++) {
            $username = $faker->userName;
            $usernameFinal = $this->slug->slug($username);
            $creator =  new User();
            $creator
                ->setEmail($usernameFinal . '@' . $faker->freeEmailDomain)
                ->setUsername($username)
                ->setPassword($this->hash->hashPassword($creator, 'premium'))
                ->setRoles(['ROLE_PREMIUM'])
            ;
            for ($i = 0; $i < 3; $i++) {
                $network = new Network();
                $network
                    ->setCreator($creator)
                    ->setName($faker->randomElement($networks))
                    ->setUrl('https://' . strtolower($faker->randomElement($networks)) . '.com/' . $admin->getUsername())
                ;
                $manager->persist($network);
            }
            $manager->persist($creator);
        }

        // 90 utilisateurs
        for ($i = 0; $i < 90; $i++) {
            $username = $faker->userName;
            $usernameFinal = $this->slug->slug($username);
            $user =  new User();
            $user
                ->setEmail($usernameFinal . '@' . $faker->freeEmailDomain)
                ->setUsername($username)
                ->setPassword($this->hash->hashPassword($user, 'user'))
                ->setRoles(['ROLE_USER'])
            ;
            for ($i = 0; $i < 3; $i++) {
                $network = new Network();
                $network
                    ->setCreator($user)
                    ->setName($faker->randomElement($networks))
                    ->setUrl('https://' . strtolower($faker->randomElement($networks)) . '.com/' . $admin->getUsername())
                ;
                $manager->persist($network);
            }
            $manager->persist($user);
        }
        $manager->flush();
    }
}
