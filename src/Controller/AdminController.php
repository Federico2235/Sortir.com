<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Security\ParticipantAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $participant = new Participant();


        $form = $this->createForm(RegistrationFormType::class, $participant);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();


            $participant->setPassword($userPasswordHasher->hashPassword($participant, $plainPassword));

            if ($form->get('administrateur')->getData()) {
                $participant->setRoles(['ROLE_ADMIN']);
                $participant->setAdministrateur(true);
            } else {
                $participant->setAdministrateur(false);
                $participant->setRoles(['ROLE_USER']);
            }
            $participant->setPhoto('avatar.jpg');


            $entityManager->persist($participant);

            $entityManager->flush();

            return $this->redirectToRoute('app_main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/participants', name: 'app_participants')]
    public function participants(EntityManagerInterface $entityManager): Response
    {
        $participants = $entityManager->getRepository(Participant::class)->findAll();
        return $this->render('admin/participants.html.twig', [
            'participants' => $participants,
        ]);
    }

    #[Route('/desactiver/{id}', name: 'app_desactiver')]
    public function desactiver(int $id, EntityManagerInterface $entityManager): Response
    {

        $participant = $entityManager->getRepository(Participant::class)->find($id);


        $participant->setActif(false);
        $entityManager->flush();
        return $this->redirectToRoute('app_participants');

    }

    #[Route('/activer/{id}', name: 'app_activer')]
    public function activer(int $id, EntityManagerInterface $entityManager): Response
    {

        $participant = $entityManager->getRepository(Participant::class)->find($id);


        $participant->setActif(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_participants');

    }

    #[Route('/supprimer/{id}', name: 'app_supprimer')]
    public function supprimer(int $id, EntityManagerInterface $entityManager): Response
    {

        $participant = $entityManager->getRepository(Participant::class)->find($id);
        $entityManager->remove($participant);
        $entityManager->flush();
        return $this->redirectToRoute('app_participants');
    }

}