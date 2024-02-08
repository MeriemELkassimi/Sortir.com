<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Faker;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VilleFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker= Faker\Factory::create('fr_FR');

        for ($i=0; $i<10; $i++){

            $ville = new Ville();
            $ville->setNom($faker->city);
            $ville->setCodePostal($faker->postcode);
            $manager->persist($ville);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['groupVille'];
    }
}
