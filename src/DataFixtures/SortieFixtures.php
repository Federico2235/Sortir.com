<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SortieFixtures extends Fixture
{
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {

        $sorties = [
            [
                'nom' => 'Pique-nique au parc',
                'dateHeureDebut' => '2025-06-10 12:00',
                'dateLimiteInscription' => '2025-06-05',
                'duree' => 90,
                'nbInscriptionsMax' => 20,
                'infosSortie' => 'Apportez votre propre repas.',
                'lieu' => 'Parc du Thabor'
            ],
            [
                'nom' => 'Visite du château',
                'dateHeureDebut' => '2025-06-15 14:00',
                'dateLimiteInscription' => '2025-06-10',
                'duree' => 120,
                'nbInscriptionsMax' => 15,
                'infosSortie' => 'Guidée par un historien.',
                'lieu' => 'Château des Ducs'
            ],
            [
                'nom' => 'Coucher de soleil à la Tour Eiffel',
                'dateHeureDebut' => '2025-06-20 20:30',
                'dateLimiteInscription' => '2025-06-15',
                'duree' => 60,
                'nbInscriptionsMax' => 30,
                'infosSortie' => 'Superbe vue garantie.',
                'lieu' => 'Tour Eiffel'
            ],
            [
                'nom' => 'Balade en vélo',
                'dateHeureDebut' => '2025-06-22 10:00',
                'dateLimiteInscription' => '2025-06-18',
                'duree' => 150,
                'nbInscriptionsMax' => 12,
                'infosSortie' => 'Casque recommandé.',
                'lieu' => 'Parc de la Tête d\'Or'
            ],
            [
                'nom' => 'Dégustation de vin',
                'dateHeureDebut' => '2025-06-25 18:00',
                'dateLimiteInscription' => '2025-06-20',
                'duree' => 90,
                'nbInscriptionsMax' => 10,
                'infosSortie' => 'Interdit aux moins de 18 ans.',
                'lieu' => 'Place de la Bourse'
            ],
            [
                'nom' => 'Balade en bateau',
                'dateHeureDebut' => '2025-06-30 16:00',
                'dateLimiteInscription' => '2025-06-25',
                'duree' => 120,
                'nbInscriptionsMax' => 25,
                'infosSortie' => 'Gilet de sauvetage fourni.',
                'lieu' => 'Vieux-Port'
            ],
            [
                'nom' => 'Concert en plein air',
                'dateHeureDebut' => '2025-07-05 19:00',
                'dateLimiteInscription' => '2025-06-30',
                'duree' => 180,
                'nbInscriptionsMax' => 50,
                'infosSortie' => 'Gratuit et ouvert à tous.',
                'lieu' => 'Capitole'
            ],
            [
                'nom' => 'Festival de musique',
                'dateHeureDebut' => '2025-07-10 21:00',
                'dateLimiteInscription' => '2025-07-05',
                'duree' => 240,
                'nbInscriptionsMax' => 100,
                'infosSortie' => 'Accès aux stands de nourriture.',
                'lieu' => 'Grand Place'
            ],
            [
                'nom' => 'Visite nocturne',
                'dateHeureDebut' => '2025-07-15 22:00',
                'dateLimiteInscription' => '2025-07-10',
                'duree' => 90,
                'nbInscriptionsMax' => 20,
                'infosSortie' => 'Lampe de poche recommandée.',
                'lieu' => 'Cathédrale de Strasbourg'
            ],
            [
                'nom' => 'Jogging matinal',
                'dateHeureDebut' => '2025-07-20 07:30',
                'dateLimiteInscription' => '2025-07-15',
                'duree' => 60,
                'nbInscriptionsMax' => 15,
                'infosSortie' => 'Échauffement avant de commencer.',
                'lieu' => 'Promenade des Anglais'
            ],
        ];

        foreach ($sorties as $data) {
            $sortie = new Sortie();
            $sortie->setNom($data['nom']);
            $sortie->setDateHeureDebut(new \DateTimeImmutable($data['dateHeureDebut']));
            $sortie->setDateLimiteInscription(new \DateTimeImmutable($data['dateLimiteInscription']));
            $sortie->setDuree($data['duree']);
            $sortie->setNbInscriptionsMax($data['nbInscriptionsMax']);
            $sortie->setInfosSortie($data['infosSortie']);

            // Récupération du lieu depuis la base de données
            $lieu = $manager->getRepository(Lieu::class)->findOneBy(['nom' => $data['lieu']]);

            if ($lieu) {
                $sortie->setLieu($lieu);
                $manager->persist($sortie);
            } else {
                echo "Lieu non trouvé : " . $data['lieu'] . "\n";
            }
        }

        $manager->flush();
    }

}
