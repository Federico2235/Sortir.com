<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\UserImporter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserImportType;

class UserImportController extends AbstractController
{
    #[Route('/admin/register', name: 'app_user_import')]
    public function import(Request $request, UserImporter $importer): Response
    {
        $form = $this->createForm(UserImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('csv_file')->getData();
            $plainPassword = $form->get('plainPassword')->getData();
            $site = $form->get('site')->getData()->getId();


            try {
                $result = $importer->import($file, $plainPassword, $site);
                $this->addFlash('success', sprintf('%d utilisateurs importés avec succès !', $result['success']));

                if (!empty($result['errors'])) {
                    $this->addFlash('warning', sprintf('%d erreurs lors de l\'import.', count($result['errors'])));
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'import: '.$e->getMessage());
            }

            return $this->redirectToRoute('app_user_import');
        }

        return $this->render('registration/user_import.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}