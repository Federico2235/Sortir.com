<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $lieux = [
            ['nom' => 'Parc du Thabor', 'rue' => 'Place Saint-Melaine', 'latitude' => 48.1147, 'longitude' => -1.6731, 'ville' => 'Rennes'],
            ['nom' => 'Château des Ducs', 'rue' => '4 Place Marc Elder', 'latitude' => 47.2155, 'longitude' => -1.5561, 'ville' => 'Nantes'],
            ['nom' => 'Tour Eiffel', 'rue' => 'Champ de Mars', 'latitude' => 48.8584, 'longitude' => 2.2945, 'ville' => 'Paris'],
            ['nom' => 'Parc de la Tête d\'Or', 'rue' => 'Boulevard des Belges', 'latitude' => 45.7772, 'longitude' => 4.8519, 'ville' => 'Lyon'],
            ['nom' => 'Place de la Bourse', 'rue' => 'Place de la Bourse', 'latitude' => 44.8412, 'longitude' => -0.5729, 'ville' => 'Bordeaux'],
            ['nom' => 'Vieux-Port', 'rue' => 'Quai des Belges', 'latitude' => 43.2951, 'longitude' => 5.3744, 'ville' => 'Marseille'],
            ['nom' => 'Capitole', 'rue' => 'Place du Capitole', 'latitude' => 43.6043, 'longitude' => 1.4437, 'ville' => 'Toulouse'],
            ['nom' => 'Grand Place', 'rue' => 'Grand Place', 'latitude' => 50.8467, 'longitude' => 4.3499, 'ville' => 'Bruxelles'],
            ['nom' => 'Cathédrale de Strasbourg', 'rue' => 'Place de la Cathédrale', 'latitude' => 48.5819, 'longitude' => 7.7508, 'ville' => 'Strasbourg'],
            ['nom' => 'Promenade des Anglais', 'rue' => 'Promenade des Anglais', 'latitude' => 43.6953, 'longitude' => 7.2566, 'ville' => 'Nice'],
        ];

        foreach ($lieux as $data) {
            $lieu = new Lieu();
            $lieu->setNom($data['nom']);
            $lieu->setRue($data['rue']);
            $lieu->setLatitude($data['latitude']);
            $lieu->setLongitude($data['longitude']);

            $ville = $manager->getRepository(Ville::class)->findOneBy(['nom' => $data['ville']]);
            if ($ville) {
                $lieu->setVille($ville);
            }

            $manager->persist($lieu);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            VilleFixtures::class,
        ];
    }
}
