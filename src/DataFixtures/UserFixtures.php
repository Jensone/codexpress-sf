<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Create admin user
        $admin = new User();
        $admin
            ->setEmail('admin@example.com')
            ->setUsername('admin')
            ->setPassword($this->hasher->hashPassword($admin, 'adminpassword'))
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        // Create regular users
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user
                ->setEmail($faker->email)
                ->setUsername($faker->userName)
                ->setPassword($this->hasher->hashPassword($user, 'userpassword'))
                ->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }

        // Create premium users
        for ($i = 0; $i < 3; $i++) {
            $premiumUser = new User();
            $premiumUser
                ->setEmail($faker->email)
                ->setUsername($faker->userName)
                ->setPassword($this->hasher->hashPassword($premiumUser, 'premiumpassword'))
                ->setRoles(['ROLE_PREMIUM']);
            $manager->persist($premiumUser);
            $this->addReference('premium_user_' . $i, $premiumUser);
        }

        $manager->flush();
    }
}
