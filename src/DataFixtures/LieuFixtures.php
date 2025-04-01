<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $villes = [
            ['nom' => 'Paris', 'codePostal' => 75001],
            ['nom' => 'Lyon', 'codePostal' => 69001],
            ['nom' => 'Marseille', 'codePostal' => 13001],
            ['nom' => 'Toulouse', 'codePostal' => 31000],
            ['nom' => 'Bordeaux', 'codePostal' => 33000],
            ['nom' => 'Lille', 'codePostal' => 59000],
            ['nom' => 'Nice', 'codePostal' => 06000],
            ['nom' => 'Strasbourg', 'codePostal' => 67000],
            ['nom' => 'Nantes', 'codePostal' => 44000],
            ['nom' => 'Montpellier', 'codePostal' => 34000],
        ];

        $lieuxParVille = [
            'Paris' => [
                ['nom' => 'Tour Eiffel', 'rue' => 'Champ de Mars', 'lat' => 48.8584, 'lon' => 2.2945],
                ['nom' => 'Louvre', 'rue' => 'Rue de Rivoli', 'lat' => 48.8606, 'lon' => 2.3376],
                ['nom' => 'Notre-Dame', 'rue' => 'Île de la Cité', 'lat' => 48.853, 'lon' => 2.3499],
                ['nom' => 'Sacré-Cœur', 'rue' => 'Butte Montmartre', 'lat' => 48.8867, 'lon' => 2.3431],
                ['nom' => 'Champs-Élysées', 'rue' => 'Avenue des Champs-Élysées', 'lat' => 48.8698, 'lon' => 2.3076]
            ],
            'Lyon' => [
                ['nom' => 'Basilique de Fourvière', 'rue' => '8 Pl. de Fourvière', 'lat' => 45.7625, 'lon' => 4.8211],
                ['nom' => 'Place Bellecour', 'rue' => 'Pl. Bellecour', 'lat' => 45.7578, 'lon' => 4.832],
                ['nom' => 'Parc de la Tête d\' or ', 'rue' => 'Boulevard des Belges', 'lat' => 45.784, 'lon' => 4.858],
                ['nom' => 'Vieux Lyon', 'rue' => 'Quai de Bondy', 'lat' => 45.764, 'lon' => 4.827],
                ['nom' => 'Musée des Confluences', 'rue' => '86 Quai Perrache', 'lat' => 45.733, 'lon' => 4.817]
            ],
            'Marseille' => [
                ['nom' => 'Vieux - Port', 'rue' => 'Quai du Port', 'lat' => 43.2965, 'lon' => 5.3698],
                ['nom' => 'Basilique Notre - Dame de la Garde', 'rue' => 'Rue Fort du Sanctuaire', 'lat' => 43.2841, 'lon' => 5.3715],
                ['nom' => 'Parc National des Calanques', 'rue' => 'Route de Cassis', 'lat' => 43.214, 'lon' => 5.445],
                ['nom' => 'Château d\'if', 'rue' => 'Île d\'if', 'lat' => 43.2814, 'lon' => 5.3251],
                ['nom' => 'Le Panier', 'rue' => 'Quartier du Panier', 'lat' => 43.2997, 'lon' => 5.3731]
            ],
            'Toulouse' => [
                ['nom' => 'Place du Capitole', 'rue' => 'Pl . du Capitole', 'lat' => 43.6043, 'lon' => 1.4437],
                ['nom' => 'Basilique Saint - Sernin', 'rue' => 'Pl . Saint - Sernin', 'lat' => 43.6095, 'lon' => 1.442],
                ['nom' => 'Cité de l\'Espace', 'rue' => 'Avenue Jean Gonord', 'lat' => 43.5866, 'lon' => 1.4825],
                ['nom' => 'Pont Neuf', 'rue' => 'Pont Neuf', 'lat' => 43.5995, 'lon' => 1.4394],
                ['nom' => 'Jardin des Plantes', 'rue' => 'Allée Jules Guesde', 'lat' => 43.5935, 'lon' => 1.451]
            ],
            'Bordeaux' => [
                ['nom' => 'Place de la Bourse', 'rue' => 'Pl. de la Bourse', 'lat' => 44.8412, 'lon' => -0.5691],
                ['nom' => 'Cité du Vin', 'rue' => '134 Quai de Bacalan', 'lat' => 44.8625, 'lon' => -0.5502],
                ['nom' => 'Pont de Pierre', 'rue' => 'Pont de Pierre', 'lat' => 44.8379, 'lon' => -0.5655],
                ['nom' => 'Parc Bordelais', 'rue' => 'Rue du Bocage', 'lat' => 44.8522, 'lon' => -0.5993],
                ['nom' => 'Cathédrale Saint-André', 'rue' => 'Pl. Pey Berland', 'lat' => 44.837, 'lon' => -0.576]
            ],
            'Lille' => [
                ['nom' => 'Grand-Place', 'rue' => 'Pl. du Général de Gaulle', 'lat' => 50.636, 'lon' => 3.063],
                ['nom' => 'Palais des Beaux-Arts', 'rue' => 'Pl. de la République', 'lat' => 50.6324, 'lon' => 3.0612],
                ['nom' => 'Vieille Bourse', 'rue' => 'Pl. du Théâtre', 'lat' => 50.6364, 'lon' => 3.0632],
                ['nom' => 'Zoo de Lille', 'rue' => 'Avenue Mathias Delobel', 'lat' => 50.6383, 'lon' => 3.0515],
                ['nom' => 'Citadelle de Lille', 'rue' => 'Av. du 43e Régiment d\'Infanterie', 'lat' => 50.6401, 'lon' => 3.0479]
            ],
            'Nice' => [
                ['nom' => 'Promenade des Anglais', 'rue' => 'Promenade des Anglais', 'lat' => 43.695, 'lon' => 7.265],
                ['nom' => 'Colline du Château', 'rue' => 'Rue des Ponchettes', 'lat' => 43.692, 'lon' => 7.281],
                ['nom' => 'Place Masséna', 'rue' => 'Pl. Masséna', 'lat' => 43.6973, 'lon' => 7.2709],
                ['nom' => 'Musée Matisse', 'rue' => '164 Avenue des Arènes de Cimiez', 'lat' => 43.7206, 'lon' => 7.276],
                ['nom' => 'Marché aux Fleurs', 'rue' => 'Cours Saleya', 'lat' => 43.6955, 'lon' => 7.273]
            ],
            'Strasbourg' => [
                ['nom' => 'Cathédrale Notre-Dame', 'rue' => 'Pl. de la Cathédrale', 'lat' => 48.5818, 'lon' => 7.7509],
                ['nom' => 'Petite France', 'rue' => 'Quartier historique', 'lat' => 48.5797, 'lon' => 7.7399],
                ['nom' => 'Parc de l\'Orangerie', 'rue' => 'Avenue de l\'Europe', 'lat' => 48.5952, 'lon' => 7.7744],
                ['nom' => 'Musée Alsacien', 'rue' => '23-25 Quai Saint-Nicolas', 'lat' => 48.5795, 'lon' => 7.7505],
                ['nom' => 'Ponts Couverts', 'rue' => 'Rue du Bain-aux-Plantes', 'lat' => 48.5792, 'lon' => 7.7388]
            ],
            'Nantes' => [
                ['nom' => 'Château des Ducs de Bretagne', 'rue' => '4 Pl. Marc Elder', 'lat' => 47.2159, 'lon' => -1.5524],
                ['nom' => 'Les Machines de l\'île', 'rue' => 'Parc des Chantiers', 'lat' => 47.2065, 'lon' => -1.5624],
                ['nom' => 'Jardin des Plantes', 'rue' => 'Rue Stanislas Baudry', 'lat' => 47.2219, 'lon' => -1.5423],
                ['nom' => 'Passage Pommeraye', 'rue' => 'Rue de la Fosse', 'lat' => 47.2145, 'lon' => -1.5587],
                ['nom' => 'Cathédrale Saint-Pierre-et-Saint-Paul', 'rue' => 'Pl. Saint-Pierre', 'lat' => 47.2183, 'lon' => -1.5525]
            ],
            'Montpellier' => [
                ['nom' => 'Place de la Comédie', 'rue' => 'Pl. de la Comédie', 'lat' => 43.6086, 'lon' => 3.8793],
                ['nom' => 'Jardin des Plantes', 'rue' => 'Boulevard Henri IV', 'lat' => 43.6163, 'lon' => 3.8735],
                ['nom' => 'Promenade du Peyrou', 'rue' => 'Rue Foch', 'lat' => 43.6111, 'lon' => 3.8728],
                ['nom' => 'Musée Fabre', 'rue' => '39 Bd Bonne Nouvelle', 'lat' => 43.6107, 'lon' => 3.8816],
                ['nom' => 'Arc de Triomphe', 'rue' => 'Rue Foch', 'lat' => 43.6113, 'lon' => 3.8724]
            ]
        ];

        for ($i = 0; $i < count($villes); $i++) {
            $ville = new Ville();
            $ville->setNom($villes[$i]['nom']);
            $ville->setCodePostal($villes[$i]['codePostal']);
            $manager->persist($ville);

            foreach ($lieuxParVille[$villes[$i]['nom']] as $lieuData) {
                $lieu = new Lieu();
                $lieu->setNom($lieuData['nom']);
                $lieu->setRue($lieuData['rue']);
                $lieu->setLatitude($lieuData['lat']);
                $lieu->setLongitude($lieuData['lon']);
                $lieu->setVille($ville);
                $manager->persist($lieu);
            }
        }

        $manager->flush();
    }
}
