<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\Etat;
use App\Entity\Participant;
use App\Form\FiltersSortiesFormType;
use App\Form\SortieType;
use App\Entity\FiltersSorties;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET', 'POST'])]
    public function index(Request $request, SortieRepository $sortieRepository): Response
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

        // ????? gestion des états dans une boucle


        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'filtersForm' => $form->createView(),
            //'sorties' => $sortieRepository->findAll(),
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
        // $sortie = new Sortie();
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
    public function removeParticipantSortie(Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        $participant = $this->getUser();
        $sortie->removeParticipant($participant);
        $entityManager->persist($participant);
        $entityManager->flush();

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/cjfms', name: 'app_sortie_cjfms')]
    public function cjfms()
    {

        return $this->render('sortie/cjfms');
    }
}
