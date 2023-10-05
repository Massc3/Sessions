<?php

namespace App\Controller;


use App\Entity\Session;
use App\Entity\Formation;
use App\Form\SessionType;
use App\Repository\FormationRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(SessionRepository $sessionRepository): Response
    {
        // $sessions = $employRepository->findAll();
        // SELECT * FROM empoye ORDER BY nom ASC;
        $sessions = $sessionRepository->findBy([], ['titre' => 'ASC']);
        // on les envoient grace a la methode render a la view index.html.twig
        return $this->render('session/index.html.twig', [
            // on fais passer la variable entreprise a laquelle on lui donne la valeur entreprise
            'sessions' => $sessions
        ]);
    }

    // #[Route('/session/new', name: 'add_session')]

    // public function add(Request $request): Response
    // {
    //     $session = new session();

    //     $form = $this->createForm(sessionType::class, $session);

    //     return $this->render('session/new.html.twig', [
    //         'formAddsession' => $form,
    //     ]);
    // }
    #[Route('/{formation_id}/session/new', name: 'add_session')]
    #[Route('/session/{id}/edit', name: 'edit_session')]
    public function new_edit(Session $session = null, $formation_id, Request $request, EntityManagerInterface $entityManager, FormationRepository $formationRepository): Response
    {

        if (!$session) {
            $session = new Session();
        }

        // just set up a fresh $task object (remove the example data)
        // on creer le formulaire a partir de sessionType
        $form = $this->createForm(SessionType::class, $session);
        // on prend en charge la requete demandé
        $form->handleRequest($request);

        $formation = $formationRepository->find($formation_id);

        // si le formulaire est rempli et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // on recupere les données du formaulaire 
            $session = $form->getData();

            $session->setFormation($formation);
            // dd($session);
            // equivalence du prepare en PDO, on prepare l'object qui va etre en base de données
            $entityManager->persist($session);
            // equivalence du execute en PDO
            $entityManager->flush();
            // on fait une redirection vers notre liste session 
            return $this->redirectToRoute('afficherDetail_formation', ['id' => $formation_id]);
        }
        return $this->render('session/new.html.twig', [
            'formAddSession' => $form,
            'edit' => $session->getId()
        ]);
    }


    #[Route('/entreprise/{id}/delete', name: 'delete_session')]
    public function delete(Session $session, EntityManagerInterface $entityManager)

    {
        $entityManager->remove($session);
        $entityManager->flush();

        return $this->redirectToRoute('app_entreprise');
    }


    #[Route('/session/{id}', name: 'afficherDetail_session')]
    public function afficherDetail(Session $session): Response
    {
        return $this->render('session/afficherDetail.html.twig', [
            // on fais passer la variable session a laquelle on lui donne la valeur session
            'session' => $session
        ]);
    }
}
