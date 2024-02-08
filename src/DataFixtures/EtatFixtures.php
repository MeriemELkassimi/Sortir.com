<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
       $etats = ['Créée', 'Ouverte', 'Clôturée', 'Activité en cours', 'Passée', 'Annulée','Archivée'];

        foreach ($etats as $et)
        {
            $etat = new Etat();
            $etat->setLibelle($et);
            $manager->persist($etat);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['groupEtat'];
    }
}
