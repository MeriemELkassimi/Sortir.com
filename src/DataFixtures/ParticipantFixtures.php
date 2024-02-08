<?php

namespace App\DataFixtures;
use App\Entity\Campus;
use App\Entity\Participant;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ParticipantFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker= Faker\Factory::create('fr_FR');


        for ($i=0; $i<10; $i++){

            $campusRepository = $manager->getRepository(Campus::class);
            $c = rand(1,5);
            $campus = $campusRepository->find($c);

            $participant = new Participant();
            $participant->setNom($faker->lastName);
            $participant->setPrenom($faker->firstName);
            $participant->setTelephone($faker->phoneNumber);
            $participant->setEmail($faker->email);
            $participant->setPseudo($faker->userName);
            $participant->setIsActif($faker->boolean);
            $participant->setPassword($faker->password);
            $participant->setRoles(["ROLE_USER"]);
            $participant->setImage('');
            $participant->setCampus($campus);


            $manager->persist($participant);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['groupParticipant'];
    }
}
