<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\Etat;
use App\Entity\Participant;
use App\Form\FiltersSortiesFormType;
use App\Form\SortieType;
use App\Entity\FiltersSorties;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Services\EtatManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET', 'POST'])]
    public function index(Request $request, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): Response
    {
        $oFilters = new FiltersSorties();
        $form = $this->createForm(FiltersSortiesFormType::class, $oFilters);
        $form->handleRequest($request);
        $oUser = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $sorties = $sortieRepository->findFilteredSorties($oFilters, $oUser);
        }
        else
        {
            $sorties = $sortieRepository->findAll($oFilters, $oUser);
            //$sorties = $sortieRepository->findCurrentSorties();
        }

        // Gestion des états dans une boucle
        // Directement dans le contrôleur
        $dateDuJour =  new \DateTime();
        $dateDuJour->format('d/m/Y H:i:s');

       foreach ($sorties as $sortie){

            $etatActuel = $sortie->getEtat()->getLibelle();

            // Méthode Hervé, avec repository
           /* if($etatActuel == "Ouverte"){
                if ($sortie->getParticipants()->count() == $sortie->getNbInscriptionsMax() || $sortie->getDateLimiteInscription() < $dateDuJour) {
              $etat = $etatRepository->findOneByLibelle("Clôturée");
              $sortie->setEtat($etat);
                }
            }*/

           //Ancienne méthode, sans repository

/*
           if($etatActuel == "Ouverte"){
               if (count($sortie->getParticipants()) == $sortie->getNbInscriptionsMax() || $sortie->getDateLimiteInscription() < $dateDuJour) {
                   $sortie->getEtat()->setLibelle("Clôturée");

               }
           }*/


           // Méthode Hervé, avec repository
           /* if($etatActuel == "Clôturée"){
                if ($sortie->getParticipants()->count() == $sortie->getNbInscriptionsMax() || $sortie->getDateLimiteInscription() < $dateDuJour) {
                    $etat = $etatRepository->findOneByLibelle("Ouverte");
                    $sortie->setEtat($etat);
                }
                $entityManager->persist($sortie);
                $entityManager->flush();
            }*/


            // Passage activité clôturée à activité en cours


           /*
           if($etatActuel == "Clôturée"){
               if ($sortie->getDateHeureDebut() == $dateDuJour){
                   $etat = $etatRepository->findOneByLibelle("Activité en cours");
                   $sortie->setEtat($etat);
               }
           }*/

           // Passage activité en cours à activité terminée

           if($etatActuel == "Activité en cours"){
               if ($sortie->getDateHeureDebut() < $dateDuJour){
                   $sortie->getEtat()->setLibelle("Passée");
               }
           }

           // Archivage des activités

         /*  if($etatActuel == "Passée" || $etatActuel == "Annulée"){
               if ($sortie->getDateHeureDebut()+30){
                   $sortie->getEtat()->setLibelle("Archivée");
               }
           }*/


            /* Changement d'état déplacé dans la fonction removeParticipant
             *
             // Quand une sortie est clôturée (nb max inscrits atteint), mais qu'un participant se désiste
           if($etatActuel == "Clôturée"){
               if ((count($sortie->getParticipants()) < $sortie->getNbInscriptionsMax()) && ($sortie->getDateLimiteInscription() >= $dateDuJour)) {
                   $sortie->getEtat()->setLibelle("Ouverte");
               }

           }
           */

            $entityManager->persist($sortie);
            $entityManager->flush();
        }


       //En utilisant un service -> lors de la mise en place, penser au paramètre dans la déclaration de fonction
       // $sorties-> $etatManager->gererEtats($sorties);



        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'filtersForm' => $form->createView(),
        ]);
    }


    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $organisateur = $this->getUser();
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etatCree = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'créée']);
            $sortie->setEtat($etatCree);
            $sortie->setOrganisateur($organisateur);
            $sortie->addParticipant($organisateur);
            $sortie->setCampus($organisateur->getCampus());

            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etatCree = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'ouverte']);
            $sortie->setEtat($etatCree);
            $entityManager->flush();

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/annuler/{id}', name: 'app_sortie_annuler', methods: ['POST', 'GET'])]
    public function annuler(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etatCree = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'annulée']);
            $sortie->setEtat($etatCree);

            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/annuler.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);


    }

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/inscrire/{id}', name: 'app_sortie_addParticipant', methods: ['GET'])]
    public function addParticipantSortie(Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
            $participant = $this->getUser();
            $sortie->addParticipant($participant);
            $entityManager->persist($participant);
            $entityManager->flush();

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/desister/{id}', name: 'app_sortie_removeParticipant', methods: ['GET'])]
    public function removeParticipantSortie(Sortie $sortie, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $dateDuJour = new \DateTime();
        $participant = $this->getUser();
        $sortie->removeParticipant($participant);
        $entityManager->persist($participant);
        $entityManager->flush();


        // Erreur SQL : violation contrainte intégrité FK => test : repasser la gestion du libellé d'état hors de la méthode remove
        // Méthode Hervé, avec repository
       /* $etatActuel = $sortie->getEtat()->getLibelle();
        if($etatActuel == "Clôturée"){
            if ($sortie->getParticipants()->count() == $sortie->getNbInscriptionsMax() || $sortie->getDateLimiteInscription() < $dateDuJour) {
                $etat = $etatRepository->findOneByLibelle("Ouverte");
                $sortie->setEtat($etat);
            }
            $entityManager->persist($sortie);
            $entityManager->flush();
        }*/

        //Ancienne méthode, sans repository

        $etatActuel = $sortie->getEtat()->getLibelle();
        if($etatActuel == "Clôturée"){
           if (count($sortie->getParticipants()) < $sortie->getNbInscriptionsMax() && $sortie->getDateLimiteInscription() >= $dateDuJour) {
                $sortie->getEtat()->setLibelle("Ouverte");
           }
            $entityManager->persist($sortie);
            $entityManager->flush();
        }


        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/cjfms', name: 'app_sortie_cjfms')]
    public function cjfms()
    {

        return $this->render('sortie/cjfms');
    }
}
