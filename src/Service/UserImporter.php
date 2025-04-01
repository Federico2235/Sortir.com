<?php

// src/Service/UserImporter.php
namespace App\Service;

use App\Entity\Participant;
use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserImporter
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function import($file, string $plainPassword, int $siteId): array
    {
        $result = [
            'success' => 0,
            'errors' => [],
        ];
        $site = $this->em->getRepository(Site::class)->find($siteId);
        if (!$site) {
            throw new \RuntimeException('Site non trouvé');
        }

        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \RuntimeException('Impossible d\'ouvrir le fichier CSV');
        }

        // Lire l'en-tête
        $headers = fgetcsv($handle, 0, ';');
        if ($headers === false || count($headers) < 5) {
            throw new \RuntimeException('Format CSV invalide');
        }

        $lineNumber = 1;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $lineNumber++;

            try {
                if (count($row) !== count($headers)) {
                    throw new \RuntimeException('Nombre de colonnes incorrect');
                }

                $data = array_combine($headers, $row);

                $user = new Participant();
                $user->setPseudo($data['pseudo']);
                $user->setNom($data['nom']);
                $user->setPrenom($data['prenom']);
                $user->setMail($data['mail']);
                $user->setTelephone($data['telephone']);
                $user->setAdministrateur('false');
                $user->setActif('true');
                $user->setRoles(["ROLE_USER"]);
                $user->setSite($site);

                // Utilisation du mot de passe fourni
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                $this->em->persist($user);
                $result['success']++;

            } catch (\Exception $e) {
                $result['errors'][] = [
                    'line' => $lineNumber,
                    'message' => $e->getMessage(),
                    'data' => $row,
                ];
            }
        }

        fclose($handle);
        $this->em->flush();

        return $result;
    }
}