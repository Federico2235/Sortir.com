<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Entity\Etat;
use App\Entity\Site;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Form\VilleType;
use App\Repository\EtatRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SortieController extends AbstractController
{
    #[Route('/create', name: 'app_createSortie', methods: ['GET', 'POST'])]
    public function createSortie(
        Request                $request,
        EntityManagerInterface $em,
        EtatRepository         $etatRepository
    ): Response
    {
        if ($request->headers->has('User-Agent') && preg_match('/Mobile|Android|iPhone|iPad/i', $request->headers->get('User-Agent'))) {
            $this->addFlash('danger', 'La création de sorties n\'est pas autorisée sur mobile.');
            return $this->redirectToRoute('app_error',['message' => "Tu es un petit malin ! Tu ne peux pas créer de sortie sur mobile."]);
        }
        // Création du formulaire Ville
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        // Création du formulaire Lieu
        $lieu = new Lieu();
        $lieu->setVille($ville);
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        // Création du formulaire Sortie
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $sortie->setOrganisateur($this->getUser());
        $sortie->setLieu($lieu);
        $sortie->setSite($sortie->getOrganisateur()->getSite());
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        // Initialisation de l'état
        $etat = $etatRepository->findOneBy(['libelle' => 'Créée']);
        $sortie->setEtat($etat);

        // Vérification des formulaires
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($villeForm->isSubmitted() && $villeForm->isValid()) {
                $em->persist($ville);
            }
            if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
                $em->persist($lieu);
            }
            if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
                $em->persist($sortie);
                $em->flush();

                $this->addFlash('success', 'Votre sortie a été créée !');
                return $this->redirect('/');
            }
        }
        return $this->render('sortie/create.html.twig', [
            'form' => $form->createView(),
            'sortieForm' => $sortieForm->createView(),
            'lieuForm' => $lieuForm->createView(),
            'villeForm' => $villeForm->createView(),
        ]);
    }

    #[Route('/detail/{id}', name: 'app_detailSortie', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detailSortie(
        int              $id,
        Request          $request,
        SortieRepository $repository,
    ): Response
    {
        $sortie = $repository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException("Sortie introuvable !");
        }

        return $this->render('sortie/read.html.twig', [
            'sortie' => $sortie
        ]);
    }

    #[Route('/inscription/{id}', name: 'inscription', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function inscription(
        int                    $id,
        SortieRepository       $repository,
        EntityManagerInterface $em
    ): Response
    {
        $sortie = $repository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée.');
        }

        /** @var Participant|null $user */
        $user = $this->getUser();

        if (!$user instanceof Participant) {
            $this->addFlash('danger', 'Vous devez être connecté pour vous inscrire.');
            return $this->redirectToRoute('app_login'); // Redirige vers la page de connexion
        }

        // Vérifie si l'utilisateur est déjà inscrit
        if ($sortie->getParticipants()->contains($user)) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie.');
            return $this->redirectToRoute('app_detailSortie', ['id' => $id]);
        }
        if($sortie->getEtat()->getLibelle() == 'Annulée') {
            $this->addFlash('danger', 'La sortie est annulée.');
            return $this->redirectToRoute('app_detailSortie', ['id' => $id]);

        }

        // Vérifie si la sortie est complète
        if ($sortie->getNbInscriptionsMax() <= count($sortie->getParticipants())) {
            $this->addFlash('danger', 'La sortie est complète.');
            return $this->redirectToRoute('app_detailSortie', ['id' => $id]);
        }

        if($sortie->getDateLimiteInscription() < new \DateTimeImmutable()) {
            $this->addFlash('danger', 'La sortie est cloturée.');
            return $this->redirectToRoute('app_detailSortie', ['id' => $id]);
        }
        if($sortie->getEtat()->getLibelle() == 'Ouverte') {

            // Ajout du participant
            $sortie->addParticipant($user);
            //$sortie->setNbInscriptions($sortie->getNbInscriptions() - 1);

            $em->flush();

            $this->addFlash('success', 'Vous êtes bien inscrit !');

            return $this->redirectToRoute('app_detailSortie', ['id' => $id]);
        }
        else{
            $this->addFlash('danger', 'Vous ne pouvez pas vous inscrire maintenant !');
            return $this->redirectToRoute('app_detailSortie', ['id' => $id]);
        }
    }
    #[Route('/desinscription/{id}', name: 'desinscription', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function desinscription(
        int                    $id,
        SortieRepository       $repository,
        EntityManagerInterface $em
    ): Response
    {
        /** @var Participant|null $user */
        $user = $this->getUser();
        $sortie = $repository->find($id);
        $sortie->removeParticipant($user);
        $em->flush();

        $this->addFlash('success', 'Vous êtes bien désinscrit !');

        return $this->redirectToRoute('app_detailSortie', ['id' => $id]);

    }


    #[Route('/annulation/{id}', name: 'annulation_confirm', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function confirmAnnulation(int $id, SortieRepository $repository): Response
    {
        $sortie = $repository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée.');
        }


        return $this->render('sortie/confirm_annulation.html.twig', [
            'sortie' => $sortie,
        ]);
    }


    #[Route('/annulation/{id}', name: 'annulation', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function annulation(
        int                    $id,
        SortieRepository       $repository,
        EntityManagerInterface $em, Request $request
    ): Response
    {
        $sortie = $repository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée.');
        }
        $motif = $request->request->get('motif');

        $now = new \DateTime();

        /** @var Participant|null $user */
        $user = $this->getUser();

        if (!$user instanceof Participant) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login'); // Redirige vers la page de connexion
        }



        if ($sortie->getDateHeureDebut() > $now) {
            $etatAnnule = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Annulée']);
            $sortie->setEtat($etatAnnule);
            $sortie->setInfosSortie($sortie->getInfosSortie() . '<br><span style="color: red; font-weight: bold;">(Annulation!!: ' . $motif . ' par : ' . $user->getNom() . ' ' . $user->getPrenom() . ')</span>');
            $em->flush(); // Mettre à jour les modifications dans la base de données $em

            $this->addFlash('success', 'Sortie annulé.');
            return $this->redirectToRoute('app_main');


        } else {
            $this->addFlash('danger', 'Impossible d\'annuler une sortie qui a déjà commencé.');
            return $this->redirectToRoute('app_main');
        }

    }

        #[Route('/publier/{id}', name: 'publier', requirements: ['id' => '\d+'], methods: ['POST'])]
        public function publier(int $id, SortieRepository $repository, EntityManagerInterface $em): Response
        {
            $sortie = $repository->find($id);

            if (!$sortie) {
                throw $this->createNotFoundException('Sortie non trouvée.');
            }

            $etatPublie = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
            $sortie->setEtat($etatPublie);
            $em->flush(); // Mettre à jour les modifications dans la base de données $em

            $this->addFlash('success', 'Sortie publiée.');
            return $this->redirectToRoute('app_main');


        }
}