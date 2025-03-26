<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Entity\Etat;
use App\Entity\Site;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Form\VilleType;
use App\Form\SiteType;
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
    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function createSortie(
        Request                $request,
        EntityManagerInterface $em,
        EtatRepository         $etatRepository
    ): Response
    {
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
        $sortie->setOrganisateur($this->getUser());
        $sortie->setLieu($lieu);
        $sortie->setSite($sortie->getOrganisateur()->getSite());
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        // Initialisation de l'état
        $etat = $etatRepository->findOneBy(['libelle' => 'Créée']);
        $sortie->setEtat($etat);

        // Vérification des formulaires
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

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'lieuForm' => $lieuForm->createView(),
            'villeForm' => $villeForm->createView(),
        ]);
    }

    #[Route('/detail/{id}', name: 'read', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function readSortie(
        int                    $id,
        Request                $request,
        SortieRepository       $repository,
    ): Response
    {
        $sortie = $repository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException("Sortie introuvable !");
        }

        return $this->render('sortie/read.html.twig',[
            'sortie' => $sortie
        ]);
    }

     # [Route('/inscription/{id}', name: 'inscription', requirements:['id' =>'\d+'], methods: ['POST'])]
    public function inscription(
        int $id,
        Request $request
    ): Response{

    }
}