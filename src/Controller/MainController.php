<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantProfileType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(SortieRepository $sortieRepository): Response
    {
$sorties=$sortieRepository->findAll();
        return $this->render('main/index.html.twig',[
            'sorties'=>$sorties
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

    #[Route('/profil/edit', name: 'app_profil_edit')]
    #[IsGranted('ROLE_USER')]
    public function modifierProfil(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
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