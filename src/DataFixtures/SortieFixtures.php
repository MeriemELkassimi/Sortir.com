<?php

namespace App\DataFixtures;
use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker= Faker\Factory::create('fr_FR');


        for ($i=0; $i<10; $i++) {

            $participantRepository = $manager->getRepository(Participant::class);
            $o = rand(1, 10);
            $organisateur = $participantRepository->find($o);

            $etatRepository = $manager->getRepository(Etat::class);
            $e = rand(1, 7);
            $etat = $etatRepository->find($e);

            $campusRepository = $manager->getRepository(Campus::class);
            $c = rand(1, 5);
            $campus = $campusRepository->find($c);

            $lieuRepository = $manager->getRepository(Lieu::class);
            $l = rand(1, 10);
            $lieu = $lieuRepository->find($l);

            $sortie = new Sortie();
            $sortie->setNom($faker->lastName);
            $sortie->setDateHeureDebut($faker->dateTimeInInterval('now','+30 days', timezone:null));
            $sortie->setDuree($faker->numberBetween(0, 600));
            $sortie->setDateLimiteInscription($faker->dateTimeInInterval('now','+30 days', timezone:null));
            $sortie->setNbInscriptionsMax($faker->numberBetween(0, 5));
            $sortie->setInfosSortie($faker->paragraph(3, true));
            $sortie->setAnnulation('');
            $sortie->setEtat($etat);
            $sortie->setCampus($campus);
            $sortie->setLieu($lieu);

            $sortie->setOrganisateur($organisateur);


            $manager->persist($sortie);
        }

            $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['groupSortie'];
    }
}
