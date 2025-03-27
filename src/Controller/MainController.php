<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantProfileType;
use App\Form\SortieFilterType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(
        SortieRepository $sortieRepository,
        Request $request,
        Security $security,
    ): Response
    {
        // Appel du fomulaire de filtres
        $filterForm = $this->createForm(SortieFilterType::class);
        $filterForm->handleRequest($request);

        // Récupération des critères de filtres
        $site = $filterForm->get('site')->getData();
        $nom = $filterForm->get('nom')->getData();
        $dateDebut = $filterForm->get('dateDebut')->getData();
        $dateFin = $filterForm->get('dateFin')->getData();
        $organisateur = $filterForm->get('organisateur')->getData();
        $inscrit = $filterForm->get('inscrit')->getData();
        $nonInscrit = $filterForm->get('nonInscrit')->getData();
        $passee = $filterForm->get('passee')->getData();

        // Création du builder de la requête qui va assembler les différents filtres
        $queryBuilder = $sortieRepository->createQueryBuilder('s');

        // Sélecteur prposeant les différents sites
        if ($site) {
            $queryBuilder->andWhere('s.site = :site')
                ->setParameter('site', $site);
        }

        // Barre de recherche par mot
        if ($nom) {
            $queryBuilder->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $nom . '%');
        }

        // INTERVAL
        //// Sélecteur début de l'interval
        if ($dateDebut) {
            $queryBuilder->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $dateDebut);
        }
        //// Sélecteur fin de l'interval
        if ($dateFin) {
            $queryBuilder->andWhere('s.dateHeureDebut <= :dateFin')
                ->setParameter('dateFin', $dateFin);
        }

        // CHECKLIST
        //// Checkpoint Utilisateur est organisateur
        if ($organisateur) {
            $queryBuilder->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $security->getUser());
        }

        //// Checkpoint Utilisateur est inscrit
        if ($inscrit) {
            $queryBuilder->andWhere(':user MEMBER OF s.participants')
                ->setParameter('user', $security->getUser());
        }

        //// Checkpoint Utilisateur n'est organisateur
        if ($nonInscrit) {
            $queryBuilder->andWhere(':user NOT MEMBER OF s.participants')
                ->setParameter('user', $security->getUser());
        }

        //// Checkpoint les Sorties sont passées
        if ($passee) {
            $queryBuilder->andWhere('s.dateHeureDebut < :now')
                ->setParameter('now', new \DateTime());
        }

        // Affichage des sorties correspondant aux critères sélectionnés
        $sorties = $queryBuilder->getQuery()->getResult();

        return $this->render('main/index.html.twig', [
            'sorties' => $sorties,
            'filterForm' => $filterForm,
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    #[Route('/profil', name: 'app_profil')]
    #[IsGranted('ROLE_USER')]
    public function profil(): Response
    {
        return $this->render('main/profil.html.twig', [
            'participant' => $this->getUser(), // Changé 'user' en 'participant' pour cohérence
        ]);
    }

    #[Route('/profil/{id}', name: 'app_profil_id', requirements: ['id' => '\d+'])]
    public function profilById(Participant $participant): Response
    {
        return $this->render('main/profil_participants.html.twig', [
            'participant' => $participant,
        ]);
    }


    #[Route('/profil/edit', name: 'app_profil_edit')]
    #[IsGranted('ROLE_USER')]
    public function modifierProfil(
        Request                     $request,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        /** @var Participant $participant */
        $participant = $this->getUser();
        $oldPassword = $participant->getPassword(); // Correction: $participant au lieu de $participant

        $form = $this->createForm(ParticipantProfileType::class, $participant, [
            'is_password_required' => false
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();

            if (!empty($newPassword)) {
                // Option: vérifier si le nouveau mot de passe est différent de l'ancien
                if ($passwordHasher->isPasswordValid($participant, $newPassword)) {
                    $this->addFlash('warning', 'Le nouveau mot de passe doit être différent de l\'actuel');
                    return $this->redirectToRoute('app_profil_edit');
                }

                $participant->setPassword(
                    $passwordHasher->hashPassword($participant, $newPassword)
                );
            } else {
                // Si aucun nouveau mot de passe, on conserve l'ancien
                $participant->setPassword($oldPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès');
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('main/profil_edit.html.twig', [
            'form' => $form->createView(),
            'participant' => $participant // Ajouté pour cohérence
        ]);
    }
}