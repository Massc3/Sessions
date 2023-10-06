<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{

    #[Route('/categorie', name: 'app_categorie')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        // $categories = $employRepository->findAll();
        // SELECT * FROM empoye ORDER BY nom ASC;
        $categories = $categorieRepository->findBy([], ['nomCategorie' => 'ASC']);
        // on les envoient grace a la methode render a la view index.html.twig
        return $this->render('categorie/index.html.twig', [
            // on fais passer la variable entreprise a laquelle on lui donne la valeur entreprise
            'categories' => $categories
        ]);
    }

    // #[Route('/categorie/new', name: 'add_categorie')]

    // public function add(Request $request): Response
    // {
    //     $categorie = new Categorie();

    //     $form = $this->createForm(CategorieType::class, $categorie);

    //     return $this->render('categorie/new.html.twig', [
    //         'formAddCategorie' => $form,
    //     ]);
    // }
    #[Route('/categorie/new', name: 'add_categorie')]
    #[Route('/categorie/{id}/edit', name: 'edit_categorie')]
    public function new_edit(Categorie $categorie = null, Request $request, EntityManagerInterface $entityManager): Response
    {

        if (!$categorie) {
            $categorie = new Categorie();
        }


        // on creer le formulaire a partir de categorieType
        $form = $this->createForm(CategorieType::class, $categorie);
        // on prend en charge la requete demandé
        $form->handleRequest($request);
        // si le formulaire est rempli et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // on recupere les données du formaulaire 
            $categorie = $form->getData();
            // equivalence du prepare en PDO, on prepare l'object qui va etre en base de données
            $entityManager->persist($categorie);
            // equivalence du execute en PDO
            $entityManager->flush();
            // on fait une redirection vers notre liste categorie 
            return $this->redirectToRoute('app_categorie');
        }
        return $this->render('categorie/new.html.twig', [
            'formAddCategorie' => $form,
            'edit' => $categorie->getId()
        ]);
    }


    #[Route('/entreprise/{id}/delete', name: 'delete_categorie')]
    public function delete(Categorie $categorie, EntityManagerInterface $entityManager)

    {
        $entityManager->remove($categorie);
        $entityManager->flush();

        return $this->redirectToRoute('app_entreprise');
    }


    #[Route('/categorie/{id}', name: 'afficherDetail_categorie')]
    public function afficherDetail(Categorie $categorie): Response
    {
        return $this->render('categorie/afficherDetail.html.twig', [
            // on fais passer la variable categorie a laquelle on lui donne la valeur categorie
            'categorie' => $categorie
        ]);
    }
}
