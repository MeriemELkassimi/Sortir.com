<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class EtatManager
{
    public function gererEtat($sorties, EntityManagerInterface $entityManager)
    {
        foreach ($sorties as $sortie) {
            $dateDuJour = new DateTime();
            $etatActuel = $sortie->getEtat()->getLibelle();
            // Vérification nb max. inscrits + date limite inscription pour éventuel changement de libellé d'état
            if ($etatActuel == "Ouverte") {
                if ((count($sortie->getParticipants()) >= $sortie->getNbInscriptionsMax()) || $sortie->getDateLimiteInscription() > $dateDuJour) {
                    $sortie->getEtat()->setLibelle("Clôturée");
                }

            }
            $entityManager->persist($sortie);
            $entityManager->flush();

            /* if($sortie->getNbInscriptionsMax() == count($sortie->getParticipants())){
                 $etat->setLibelle("Clôturée");
                 $sortie->setEtat($etat);
               $sortie->setEtat()->setLibelle("Clôturée");
             }
            return $sortie;
            */
        }
    }
}