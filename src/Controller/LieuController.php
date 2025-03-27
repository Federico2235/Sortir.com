<?php

namespace App\Controller;

use App\Entity\Lieu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LieuController extends AbstractController
{
    #[\Symfony\Component\Routing\Annotation\Route('/get-lieux', name: 'get_lieux', methods: ['GET'])]
    public function getLieux(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $villeId = $request->query->get('ville_id');
        if (!$villeId) {
            return new JsonResponse(['error' => 'Aucune ville sélectionnée'], 400);
        }

        $lieux = $em->getRepository(Lieu::class)->findBy(['ville' => $villeId]);

        $response = [];
        foreach ($lieux as $lieu) {
            $response[] = [
                'id' => $lieu->getId(),
                'nom' => $lieu->getNom()
            ];
        }

        return new JsonResponse($response);
    }
}
