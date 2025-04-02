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
    #[IsGranted('ROLE_USER')]
    #[Route('/create', name: 'app_createSortie', methods: ['GET', 'POST'])]
    public function createSortie(Request $request, SortieRepository $repository): Response
    {
        if ($request->headers->has('User-Agent') && preg_match('/Mobile|Android|iPhone|iPad/i', $request->headers->get('User-Agent'))) {
            return $this->redirectToRoute('app_error', ['message' => "Tu es un petit malin ! Tu ne peux pas créer de sortie sur mobile."]);
        }

        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->createSortie($sortie, $this->getUser());
            $this->addFlash('success', 'Votre sortie a été créée !');
            return $this->redirect('/');
        }

        $data = $repository->getVillesAndLieux();

        return $this->render('sortie/create.html.twig', [
            'form' => $form->createView(),
            'sortieForm' => $form->createView(), // Pourquoi deux fois le même formulaire ?
            'villes' => $data['villes'],
            'lieux' => $data['lieux']
        ]);
    }

    #[Route('/detail/{id}', name: 'app_detailSortie', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detailSortie(int $id, SortieRepository $repository): Response
    {
        $sortie = $repository->getSortieDetails($id);

        if (!$sortie) {
            return $this->redirectToRoute('app_error', [
                'message' => "Cette sortie n'existe pas ou a été supprimée.",
                'status_code' => 404
            ]);
        }

        return $this->render('sortie/read.html.twig', ['sortie' => $sortie]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/ajouterLieu', name: 'app_ajouterLieu', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function ajouterLieu(
        Request          $request,
        EntityManagerInterface $em,
    ): Response
    {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $em->persist($lieu);
            $em->flush();
            return $this->redirectToRoute('app_createSortie');
        }

        return $this->render('sortie/ajouterLieu.html.twig', [
            'lieuForm' => $lieuForm
        ]);


    }


    #[IsGranted('ROLE_USER')]
    #[Route ('/ajouterVille', name: 'app_ajouterVille', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function ajouterVille(
        Request          $request,
        EntityManagerInterface $em,
    ): Response
    {
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);
        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            $em->persist($ville);
            $em->flush();
            return $this->redirectToRoute('app_ajouterLieu');

        }
        return $this->render('sortie/ajouterVille.html.twig', [
            'villeForm' => $villeForm
        ]) ;
    }



    #[Route('/inscription/{id}', name: 'inscription', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function inscription(int $id, SortieRepository $repository): Response
    {
        $sortie = $repository->find($id);
        $user = $this->getUser();

        if (!$sortie || !$user instanceof Participant) {
            $this->addFlash('danger', 'Action impossible.');
            return $this->redirectToRoute($sortie ? 'app_detailSortie' : 'app_login', ['id' => $id]);
        }

        if ($repository->inscrireParticipant($sortie, $user)) {
            $this->addFlash('success', 'Vous êtes bien inscrit !');
        } else {
            $this->addFlash('danger', 'Inscription impossible !');
        }

        return $this->redirectToRoute('app_detailSortie', ['id' => $id]);
    }

    #[Route('/desinscription/{id}', name: 'desinscription', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function desinscription(int $id, SortieRepository $repository): Response
    {
        $sortie = $repository->find($id);
        $user = $this->getUser();

        if ($sortie && $user instanceof Participant) {
            $repository->desinscrireParticipant($sortie, $user);
            $this->addFlash('success', 'Vous êtes bien désinscrit !');
        }

        return $this->redirectToRoute('app_detailSortie', ['id' => $id]);
    }

    #[isGranted('ROLE_ADMIN')]
    #[Route('/annulation/{id}', name: 'annulation_confirm', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function confirmAnnulation(int $id, SortieRepository $repository): Response
    {
        $sortie = $repository->find($id);
        return $this->render('sortie/confirm_annulation.html.twig', ['sortie' => $sortie]);
    }

    #[isGranted('ROLE_ADMIN')]
    #[Route('/annulation/{id}', name: 'annulation', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function annulation(int $id, Request $request, SortieRepository $repository): Response
    {
        $sortie = $repository->find($id);
        $motif = $request->request->get('motif');
        $user = $this->getUser();

        if ($sortie && $user instanceof Participant && $repository->annulerSortie($sortie, $motif, $user)) {
            $this->addFlash('success', 'Sortie annulée.');
        } else {
            $this->addFlash('danger', 'Impossible d\'annuler cette sortie.');
        }

        return $this->redirectToRoute('app_main');
    }

    #[Route('/publier/{id}', name: 'publier', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function publier(int $id, SortieRepository $repository): Response
    {
        $sortie = $repository->find($id);

        if ($sortie) {
            $repository->publierSortie($sortie);
            $this->addFlash('success', 'Sortie publiée.');
        }

        return $this->redirectToRoute('app_main');
    }

    #[isGranted('ROLE_ADMIN')]
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST','GET'])]
    public function delete(int $id, SortieRepository $repository): Response
    {
        $sortie = $repository->find($id);

        if ($sortie) {
            $repository->deleteSortie($sortie);
            $this->addFlash('success', 'Sortie supprimée.');
        }

        return $this->redirectToRoute('app_main');
    }
}