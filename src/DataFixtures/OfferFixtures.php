<?php

namespace App\DataFixtures;

use App\Entity\Offer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class OfferFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $offer = new Offer();
        $manager->persist($offer);

        $manager->flush();
    }
}
