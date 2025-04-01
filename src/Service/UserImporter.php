<?php

namespace App\Service;

use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserImporter
{
    private $em;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    public function import($file): array
    {
        $result = [
            'success' => 0,
            'errors' => [],
        ];

        // Ouvrir le fichier CSV
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \RuntimeException('Impossible d\'ouvrir le fichier CSV');
        }

        // Lire l'en-tête
        $headers = fgetcsv($handle, 0, ';');
        if ($headers === false || count($headers) < 5) {
            throw new \RuntimeException('Format CSV invalide');
        }

        // Lire les lignes
        $lineNumber = 1;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $lineNumber++;

            try {
                // Vérifier que la ligne a le bon nombre de colonnes
                if (count($row) !== count($headers)) {
                    throw new \RuntimeException('Nombre de colonnes incorrect');
                }

                // Créer un tableau associatif
                $data = array_combine($headers, $row);

                // Créer un nouvel utilisateur
                $user = new Participant();
                $user->setPseudo($data['pseudo']);
                $user->setNom($data['nom']);
                $user->setPrenom($data['prenom']);
                $user->setMail($data['mail']);
                $user->setTelephone($data['telephone']);
                $user->setAdministrateur('false');
                $user->setActif('true');
                $user->setPassword('123456');
                $user->setRoles(["ROLE_USER"]); // Assurez-vous que la valeur est un tableau ROLE_USER');
                $user->setActif('true');


                // Générer un mot de passe aléatoire
                $plainPassword = bin2hex(random_bytes(8));
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                // Enregistrer l'utilisateur
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