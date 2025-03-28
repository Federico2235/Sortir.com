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

            // encode the plain password
            $participant->setPassword($userPasswordHasher->hashPassword($participant, $plainPassword));

            if ($form->get('administrateur')->getData()) {
                $participant->setRoles(['ROLE_ADMIN']);
                $participant->setAdministrateur(true);
            } else {
                $participant->setAdministrateur(false);
            }


            $entityManager->persist($participant);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($participant, ParticipantAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    //desactiver utilisateur
//    #[Route('/desactiver/{id}', name: 'app_desactiver')]
//    public function desactiver($id, EntityManagerInterface $entityManager): Response
//    {
//        $participant = $entityManager->getRepository(Participant::class)->find($id);
//
//        if (!$participant) {
//            throw $this->createNotFoundException('No participant found for id ' . $id);
//        }
//
//        $participant->setActif(false);
//        $entityManager->persist($participant);
//        $entityManager->flush();
//
//        return $this->redirectToRoute('app_main');
//    }

}