<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Form\VilleType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SortieController extends AbstractController
{
    public function __construct(
        private readonly SortieRepository $sortieRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/create', name: 'app_createSortie', methods: ['GET', 'POST'])]
    public function createSortie(Request $request): Response
    {
        if ($request->headers->has('User-Agent') && preg_match('/Mobile|Android|iPhone|iPad/i', $request->headers->get('User-Agent'))) {
            return $this->redirectToRoute('app_error', ['message' => "Tu es un petit malin ! Tu ne peux pas créer de sortie sur mobile."]);
        }

        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->sortieRepository->createSortie($sortie, $this->getUser());
            $this->addFlash('success', 'Votre sortie a été créée !');
            return $this->redirectToRoute('app_main');
        }

        $villesWithLieux = $this->sortieRepository->getVillesWithLieux();

        return $this->render('sortie/create.html.twig', [
            'form' => $form->createView(), // Un seul nom de variable pour le formulaire
            'villes' => $villesWithLieux,
        ]);
    }

    #[Route('/detail/{id}', name: 'app_detailSortie', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detailSortie(int $id): Response
    {
        $sortie = $this->sortieRepository->getSortieDetails($id);

        if (!$sortie) {
            return $this->redirectToRoute('app_error', [
                'message' => "Cette sortie n'existe pas ou a été supprimée.",
                'status_code' => 404
            ]);
        }

        return $this->render('sortie/read.html.twig', [
            'sortie' => $sortie
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/ajouterLieu', name: 'app_ajouterLieu', methods: ['GET', 'POST'])]
    public function ajouterLieu(Request $request): Response
    {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $this->em->persist($lieu);
            $this->em->flush();

            // Recharge les villes avec leurs lieux après ajout
            $villesWithLieux = $this->sortieRepository->getVillesWithLieux();
            $this->addFlash('success', 'Lieu ajouté avec succès !');

            return $this->redirectToRoute('app_createSortie', [
                'villes' => $villesWithLieux
            ]);
        }

        return $this->render('sortie/ajouterLieu.html.twig', [
            'lieuForm' => $lieuForm->createView()
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/ajouterVille', name: 'app_ajouterVille', methods: ['GET', 'POST'])]
    public function ajouterVille(Request $request): Response
    {
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            $this->em->persist($ville);
            $this->em->flush();

            $this->addFlash('success', 'Ville ajoutée avec succès !');
            return $this->redirectToRoute('app_ajouterLieu');
        }

        return $this->render('sortie/ajouterVille.html.twig', [
            'villeForm' => $villeForm->createView()
        ]);
    }

    #[Route('/inscription/{id}', name: 'inscription', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function inscription(int $id): Response
    {
        $user = $this->getUser();
        $sortie = $this->sortieRepository->find($id);

        if (!$sortie || !$user instanceof Participant) {
            $this->addFlash('danger', 'Action impossible.');
            return $this->redirectToRoute($sortie ? 'app_detailSortie' : 'app_login', ['id' => $id]);
        }

        if ($this->sortieRepository->inscrireParticipant($sortie, $user)) {
            $this->addFlash('success', 'Vous êtes bien inscrit !');
        } else {
            $this->addFlash('danger', 'Inscription impossible !');
        }

        return $this->redirectToRoute('app_detailSortie', ['id' => $id]);
    }

    #[Route('/desinscription/{id}', name: 'desinscription', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function desinscription(int $id): Response
    {
        $sortie = $this->sortieRepository->find($id);
        $user = $this->getUser();

        if ($sortie && $user instanceof Participant) {
            $this->sortieRepository->desinscrireParticipant($sortie, $user);
            $this->addFlash('success', 'Vous êtes bien désinscrit !');
        }

        return $this->redirectToRoute('app_detailSortie', ['id' => $id]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/annulation/{id}', name: 'annulation_confirm', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function confirmAnnulation(int $id): Response
    {
        $sortie = $this->sortieRepository->find($id);
        return $this->render('sortie/confirm_annulation.html.twig', [
            'sortie' => $sortie
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/annulation/{id}', name: 'annulation', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function annulation(int $id, Request $request): Response
    {
        $sortie = $this->sortieRepository->find($id);
        $motif = $request->request->get('motif');
        $user = $this->getUser();

        if ($sortie && $user instanceof Participant && $this->sortieRepository->annulerSortie($sortie, $motif, $user)) {
            $this->addFlash('success', 'Sortie annulée.');
        } else {
            $this->addFlash('danger', 'Impossible d\'annuler cette sortie.');
        }

        return $this->redirectToRoute('app_main');
    }

    #[Route('/publier/{id}', name: 'publier', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function publier(int $id): Response
    {
        $sortie = $this->sortieRepository->find($id);

        if ($sortie) {
            $this->sortieRepository->publierSortie($sortie);
            $this->addFlash('success', 'Sortie publiée.');
        }

        return $this->redirectToRoute('app_main');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST','GET'])]
    public function delete(int $id): Response
    {
        $sortie = $this->sortieRepository->find($id);

        if ($sortie) {
            $this->sortieRepository->deleteSortie($sortie);
            $this->addFlash('success', 'Sortie supprimée.');
        }

        return $this->redirectToRoute('app_main');
    }
}