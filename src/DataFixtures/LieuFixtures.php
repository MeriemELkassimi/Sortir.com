<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class LieuFixtures extends Fixture implements FixtureGroupInterface
{

    public function load(ObjectManager $manager): void
    {

        $faker= Faker\Factory::create('fr_FR');


        for ($i=0; $i<10; $i++){

            $villeRepository = $manager->getRepository(Ville::class);
            $v = rand(1,9);
            $ville = $villeRepository->find($v);

         $lieu = new Lieu();
         $lieu->setNom('nom_lieu');
         $lieu->setVille($ville);
         $lieu->setRue($faker->streetName);
         $lieu->setLatitude($faker->latitude($min = -90, $max = 90));
         $lieu->setLongitude($faker->longitude ($min = -180, $max = 180));
         $manager->persist($lieu);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['groupLieu'];
    }
}
