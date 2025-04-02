<?php


namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use DateTime;
use function Symfony\Component\String\s;

class SortieFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $sortiesData = [
            ['Concert Rock', '2025-04-10 20:00:00', 1, 50],
            ['Soirée Cinéma', '2025-04-12 18:30:00', 2, 30],
            ['Randonnée en Montagne', '2025-04-15 09:00:00', 3, 20],
            ['Dîner Gastronomique', '2025-04-20 19:00:00', 4, 15],
            ['Tournoi de Football', '2025-04-25 14:00:00', 5, 40],
            ['Exposition d\'Art', '2025-04-28 16:00:00', 6, 25],
            ['Balade en Vélo', '2025-05-02 10:00:00', 7, 12],
            ['Cours de Cuisine', '2025-05-05 17:00:00', 8, 8],
            ['Spectacle de Magie', '2025-05-10 20:30:00', 9, 35],
            ['Atelier de Peinture', '2025-05-15 15:00:00', 10, 10],
        ];

        foreach ($sortiesData as $index => [$nom, $date, $lieuIndex, $places]) {
            $sortie = new Sortie();
            $sortie->setNom($nom);
            $sortie->setLieu($this->getReference('lieu_' . $lieuIndex,Lieu::class));
            $sortie->setNbInscriptionsMax($places);
            $sortie->setDuree(120);
            $dateHeureDebut = new DateTimeImmutable($date);
            $sortie->setDateHeureDebut($dateHeureDebut);
            $dateLimiteInscription = $dateHeureDebut->modify('-2 days');
            $sortie->setDateLimiteInscription($dateLimiteInscription);
            $sortie->setInfosSortie('Información adicional sobre la sortie: ' . $nom);
           $etat = $this->getReference('etat_'.rand(1,6),Etat::class);
            $sortie->setEtat($etat);
            $organisateur = $this->getReference('participant_' . rand(2, 3),Participant::class);
            $sortie->setOrganisateur($organisateur);
            $sortie->setSite($organisateur->getSite());


            $manager->persist($sortie);
            $this->addReference('sortie_' . ($index + 1), $sortie);

        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LieuFixtures::class,
            ParticipantFixtures::class,
        ];
    }
}

