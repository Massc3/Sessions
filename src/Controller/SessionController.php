<?php

namespace App\Controller;


use App\Entity\Session;
use App\Entity\Formation;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use App\Repository\FormationRepository;
use App\Repository\StagiaireRepository;
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


    #[Route('/session/{id}/delete', name: 'delete_session')]
    public function delete(Session $session, EntityManagerInterface $entityManager)

    {
        $entityManager->remove($session);
        $entityManager->flush();

        return $this->redirectToRoute('app_session');
    }


    #[Route('/session/{id}', name: 'afficherDetail_session')]
    public function afficherDetail(Session $session, SessionRepository $sessionRepository): Response
    {

        $stagiaireNotSession = $sessionRepository->findByStagiairesNotInSession($session->getId());

        return $this->render('session/afficherDetail.html.twig', [
            // on fais passer la variable session a laquelle on lui donne la valeur session
            'session' => $session,
            'stagiaireNotSession' => $stagiaireNotSession
        ]);
    }

    #[Route('/{stagiaire_id}/session/{id}', name: 'add_stagiaireInSession')]
    public function addStagiaireToSession(StagiaireRepository $stagiaireRepository, EntityManagerInterface $entityManager, Session $session, $stagiaire_id)
    {
        $stagiaire = $stagiaireRepository->find($stagiaire_id);

        if ($stagiaire) {
            // Ajoutez le stagiaire à la session
            $session->addStagiaire($stagiaire);
            // Enregistrez les modifications dans la base de données
            $entityManager->persist($session); // Prépare l'insertion en base de données
            $entityManager->flush(); // Exécute l'insertion en base de données
            // Redirigez l'utilisateur vers la page actuelle (ou une autre page si nécessaire)
            return $this->redirectToRoute('afficherDetail_session', ['id' => $session->getId()]);
        }
    }


    #[Route('/remove/{stagiaire_id}/session/{id}', name: 'remove_session')]
    public function removeStagiaireToSession(StagiaireRepository $stagiaireRepository, EntityManagerInterface $entityManager, Session $session, Request $request, $stagiaire_id)
    {
        $stagiaire = $stagiaireRepository->find($stagiaire_id);
        if ($stagiaire) {
            // Retirer le stagiaire à la session
            $session->removeStagiaire($stagiaire);
            // Enregistrez les modifications dans la base de données
            $entityManager->persist($session); // Prépare l'insertion en base de données
            // dd($session->getId());
            $entityManager->flush(); // Exécute l'insertion en base de données
            // Redirigez l'utilisateur vers la page actuelle (ou une autre page si nécessaire)
            return $this->redirectToRoute('afficherDetail_session', ['id' => $session->getId()]);
        }
    }
}
