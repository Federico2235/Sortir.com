<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ApiLieuController extends AbstractController
{
    #[Route('/villes/{villeId}/lieux', name: 'api_lieux_by_ville', methods: ['GET'])]
    public function getLieuxParVille(int $villeId, LieuRepository $lieuRepository): JsonResponse
    {
        // Récupérer les lieux liés à la ville donnée
        $lieux = $lieuRepository->findBy(['ville' => $villeId]);

        // Transformer en JSON
        $data = array_map(fn($lieu) => [
            'id' => $lieu->getId(),
            'nom' => $lieu->getNom(),
        ], $lieux);

        return $this->json($data);
    }
}
