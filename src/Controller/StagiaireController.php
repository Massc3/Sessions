<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(StagiaireRepository $stagiaireRepository): Response
    {
        // $stagiaire = $employRepository->findAll();
        // SELECT * FROM empoye ORDER BY nom ASC;
        $stagiaire = $stagiaireRepository->findBy([], ['nom' => 'ASC']);
        // on les envoient grace a la methode render a la view index.html.twig
        return $this->render('stagiaire/index.html.twig', [
            // on fais passer la variable stagiaire a laquelle on lui donne la valeur stagiaire
            'stagiaire' => $stagiaire
        ]);
    }

    #[Route('/stagiaire', name: 'noSub_stagiaire')]
    public function addStagiaire(StagiaireRepository $stagiaireRepository): Response
    {
        // $stagiaire = $employRepository->findAll();
        // SELECT * FROM empoye ORDER BY nom ASC;
        $stagiaire = $stagiaireRepository->find();
        // on les envoient grace a la methode render a la view index.html.twig
        return $this->render('stagiaire/index.html.twig', [
            // on fais passer la variable stagiaire a laquelle on lui donne la valeur stagiaire
            'stagiaire' => $stagiaire
        ]);
    }

    #[Route('/stagiaire/new', name: 'add_stagiaire')]
    #[Route('/stagiaire/{id}/edit', name: 'edit_stagiaire')]
    public function new_edit(Stagiaire $stagiaire = null, Request $request, EntityManagerInterface $entityManager): Response
    {

        if (!$stagiaire) {
            $stagiaire = new Stagiaire();
        }

        // on creer le formulaire a partir de stagiaireType
        $form = $this->createForm(StagiaireType::class, $stagiaire);
        // on prend en charge la requete demandé
        $form->handleRequest($request);
        // si le formulaire est rempli et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // on recupere les données du formaulaire 
            $stagiaire = $form->getData();
            // equivalence du prepare en PDO, on prepare l'object qui va etre en base de données
            $entityManager->persist($stagiaire);
            // equivalence du execute en PDO
            $entityManager->flush();
            // on fait une redirection vers notre liste stagiaire 
            return $this->redirectToRoute('app_stagiaire');
        }
        return $this->render('stagiaire/new.html.twig', [
            'formAddstagiaire' => $form,
            'edit' => $stagiaire->getId()
        ]);
    }

    #[Route('/stagiaire/{id}/delete', name: 'delete_stagiaire')]
    public function delete(Stagiaire $stagiaire, EntityManagerInterface $entityManager)

    {
        $entityManager->remove($stagiaire);
        $entityManager->flush();

        return $this->redirectToRoute('app_stagiaire');
    }

    #[Route('/stagiaire/{id}', name: 'afficherDetail_stagiaire')]
    public function afficherDetail(Stagiaire $stagiaire): Response
    {
        return $this->render('stagiaire/afficherDetail.html.twig', [
            // on fais passer la variable stagiaire a laquelle on lui donne la valeur stagiaire
            'stagiaire' => $stagiaire
        ]);
    }
}
