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
use App\Repository\VilleRepository;
use DateInterval;
use DateTime;
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
        EtatRepository         $etatRepository,
        VilleRepository       $villeRepository
    ): Response
    {
        if ($request->headers->has('User-Agent') && preg_match('/Mobile|Android|iPhone|iPad/i', $request->headers->get('User-Agent'))) {
            return $this->redirectToRoute('app_error', ['message' => "Tu es un petit malin ! Tu ne peux pas créer de sortie sur mobile."]);
        }

        // Création du formulaire Ville
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);

        // Création du formulaire Lieu
        $lieu = new Lieu();
        $lieu->setVille($ville);
        $lieuForm = $this->createForm(LieuType::class, $lieu);

        // Création du formulaire Sortie
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());
        $sortie->setLieu($lieu);
        $sortie->setSite($sortie->getOrganisateur()->getSite());

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        // Manejo de los formularios
        $villeForm->handleRequest($request);
        $lieuForm->handleRequest($request);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            if ($villeForm->isSubmitted() && $villeForm->isValid()) {
                $existingVille = $villeRepository->findOneBy(['nom' => $ville->getNom()]);

                if ($existingVille) {
                    // Si la ciudad ya existe, usar la existente
                    $ville = $existingVille;
                } else {
                    // Si la ciudad no existe, persistir la nueva
                    $em->persist($ville);

                }
            }
            if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
                $lieu->setVille($ville); // Asignar la ciudad al objeto $lieu
                $em->persist($lieu);
            }

            // Inicialización de l'état
            $etat = $etatRepository->findOneBy(['libelle' => 'Créée']);
            $sortie->setEtat($etat);

            // Persistir y guardar la sortie
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Votre sortie a été créée !');
            return $this->redirectToRoute('app_main'); // Asegúrate de cambiar esto según la ruta correcta.
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'lieuForm' => $lieuForm->createView(),
            'villeForm' => $villeForm->createView(),
        ]);
    }




    #[Route('/detail/{id}', name: 'app_detailSortie', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detailSortie(
        int              $id,
        Request          $request,
        SortieRepository $repository
    ): Response
    {


        $sortie = $repository->find($id);

        if (!$sortie || $sortie->getId() === null) {
            return $this->redirectToRoute('app_error', [
                'message' => "Cette sortie n'existe pas ou a été supprimée.",
                'status_code' => 404
            ]);
        }

        if ($sortie->getDateHeureDebut() < (new DateTime())->sub(new DateInterval('P30D'))) {
            return $this->redirectToRoute('app_error', ['message' => "cette sortie n'existe pas."]);
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
        if ($sortie->getEtat()->getLibelle() == 'Annulée') {
            $this->addFlash('danger', 'La sortie est annulée.');
            return $this->redirectToRoute('app_detailSortie', ['id' => $id]);

        }

        // Vérifie si la sortie est complète
        if ($sortie->getNbInscriptionsMax() <= count($sortie->getParticipants())) {
            $this->addFlash('danger', 'La sortie est complète.');
            return $this->redirectToRoute('app_detailSortie', ['id' => $id]);
        }

        if ($sortie->getDateLimiteInscription() < new \DateTimeImmutable()) {
            $this->addFlash('danger', 'La sortie est cloturée.');
            return $this->redirectToRoute('app_detailSortie', ['id' => $id]);
        }
        if ($sortie->getEtat()->getLibelle() == 'Ouverte') {

            // Ajout du participant
            $sortie->addParticipant($user);
            //$sortie->setNbInscriptions($sortie->getNbInscriptions() - 1);

            $em->flush();

            $this->addFlash('success', 'Vous êtes bien inscrit !');

            return $this->redirectToRoute('app_detailSortie', ['id' => $id]);
        } else {
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