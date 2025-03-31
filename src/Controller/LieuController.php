<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Flex\Response;

final class LieuController extends AbstractController
{
    public function __construct(LieuRepository $lieuRepository)
    {
    }

    #[Route('/ville={id}', name: 'app_lieu', methods: ['POST'])]
    public function index(
        int $id
    ): JsonResponse
    {
        $lieu = this->lieuRepository->findby(['ville' => $id]);
        return  $this->json($lieu, 200, [], ['groups' => 'lieu']);

    }
}
