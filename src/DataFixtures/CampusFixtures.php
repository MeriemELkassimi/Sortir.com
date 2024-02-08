<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
       $campuses = ['Rennes', 'Nantes', 'Niort', 'Campus en ligne', 'Quimper'];

        foreach ($campuses as $cp)
        {
            $campus = new Campus();
            $campus->setNom($cp);
            $manager->persist($campus);
        }

        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['groupCampus'];
    }
}
