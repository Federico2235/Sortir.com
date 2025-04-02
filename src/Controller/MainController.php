<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Form\ParticipantProfileType;
use App\Form\SortieFilterType;
use App\Repository\EtatRepository;
use App\Service\SortieEtatUpdater;
use App\Service\Uploader;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function Symfony\Component\String\s;

final class MainController extends AbstractController
{
    public function __construct(
        private SortieEtatUpdater $sortieEtatUpdater,
    )
    {}

    /**
     * @throws \DateMalformedStringException
     */
    #[Route('/', name: 'app_main')]
    public function index(
        SortieRepository $sortieRepository,
        Request          $request,
        Security         $security,
    ): Response
    {

        // Méthode de mise à jour des états des sorties
        $this->sortieEtatUpdater->updateEtats();

        // Formulaire de filtres
        $filterForm = $this->createForm(SortieFilterType::class);
        $filterForm->handleRequest($request);

        // Création des filtres à partir du formulaire
        $filters = $filterForm->getData();
        $sorties = $sortieRepository->sortieFilters($filters, $security->getUser());

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
    public function profil(): Response
    {
        return $this->render('main/profil.html.twig', [
            'participant' => $this->getUser(),
        ]);
    }

    #[Route('/profil/{id}', name: 'app_profil_id', requirements: ['id' => '\d+'])]
    public function profilById(Participant $participant): Response
    {
        return $this->render('main/profil_participants.html.twig', [
            'participant' => $participant,
        ]);
    }


    #[isGranted ('ROLE_USER')]
    #[Route('/profil/edit', name: 'app_profil_edit')]
    public function modifierProfil(
        Request                     $request,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        Uploader                    $uploader
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

            /**
             * @var UploadedFile|null $photo
             */
            $photo = $form->get('photo')->getData();

            if ($photo !== null) {

                $oldPhoto = $participant->getPhoto();
                if ($oldPhoto) {
                    $uploader->delete($oldPhoto, $this->getParameter('participant_photo_dir'));
                }


                $newFileName = $uploader->save(
                    $photo,
                    $participant->getNom(),
                    $this->getParameter('participant_photo_dir')
                );

                $participant->setPhoto($newFileName);
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

    #[Route('/error/{message}', name: 'app_error', requirements: ['message' => '.+'])]
    public function error(string $message): Response
    {
        return $this->render('main/error.html.twig', [
            'message' => $message
        ]);
    }
}