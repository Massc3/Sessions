<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\SessionType;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(FormationRepository $formationRepository): Response
    {
        // $formations = $employRepository->findAll();
        // SELECT * FROM empoye ORDER BY nom ASC;
        $formations = $formationRepository->findBy([], ['nomFormation' => 'ASC']);
        // on les envoient grace a la methode render a la view index.html.twig
        return $this->render('formation/index.html.twig', [
            // on fais passer la variable entreprise a laquelle on lui donne la valeur entreprise
            'formations' => $formations
        ]);
    }

    #[Route('/formation/new', name: 'add_formation')]
    #[Route('/formation/{id}/edit', name: 'edit_formation')]
    public function new_edit(Formation $formation = null, Request $request, EntityManagerInterface $entityManager): Response
    {

        if (!$formation) {
            $formation = new Formation();
        }
        // just set up a fresh $task object (remove the example data)
        // on creer le formulaire a partir de formationType
        $form = $this->createForm(FormationType::class, $formation);
        // on prend en charge la requete demandé
        $form->handleRequest($request);
        // si le formulaire est rempli et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // on recupere les données du formaulaire 
            $formation = $form->getData();
            // equivalence du prepare en PDO, on prepare l'object qui va etre en base de données
            $entityManager->persist($formation);
            // equivalence du execute en PDO
            $entityManager->flush();
            // on fait une redirection vers notre liste formation 
            return $this->redirectToRoute('app_formation');
        }
        return $this->render('formation/new.html.twig', [
            'formAddFormation' => $form,
            'edit' => $formation->getId()
        ]);
    }

    #[Route('/session/{id}/delete', name: 'delete_formation')]
    public function delete(Formation $formation, EntityManagerInterface $entityManager)

    {
        $entityManager->remove($formation);
        $entityManager->flush();

        return $this->redirectToRoute('app_formation');
    }


    #[Route('/formation/{id}', name: 'afficherDetail_formation')]
    public function afficherDetail(Formation $formation): Response
    {
        return $this->render('formation/afficherDetail.html.twig', [
            // on fais passer la variable formation a laquelle on lui donne la valeur formation
            'formation' => $formation
        ]);
    }

    public function addSession(ManagerRegistry $doctrine, Formation $formation, Session $session = null, Request $request, EntityManagerInterface $entityManager)
    {
        if (!$session) {
            $session = new Session();
        }

        // on creer le formulaire a partir de sessionType
        $form = $this->createForm(SessionType::class, $session);
        // on prend en charge la requete demandé
        $form->handleRequest($request);
        // si le formulaire est rempli et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // on recupere les données du formaulaire 
            // $formation = addSession($session);
            // equivalence du prepare en PDO, on prepare l'object qui va etre en base de données
            $entityManager = $doctrine->getManager();
            $entityManager->persist($session);
            // equivalence du execute en PDO
            $entityManager->flush();
            // on fait une redirection vers notre liste session 
            return $this->redirectToRoute('app_session');
        }
        return $this->render('session/new.html.twig', [
            'form' => $form,
            'formation' => $formation->getId(),
            'sessionId' => $session->getId()
        ]);
    }
}
