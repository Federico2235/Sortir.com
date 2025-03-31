<?php

namespace App\Controller;

use App\Entity\Lieu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class LieuController extends AbstractController
{
    #[Route('/ville={id}', name: 'app_lieu', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $lieuxOfCity = $entityManager->getRepository()->findBy(['ville' => $id]);

        return $this->json($lieuxOfCity);
    }
}
