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
                ['nom' => 'Tour Eiffel', 'rue' => 'Av. Gustave Eiffel', 'lat' => 48.858370, 'lon' => 2.294480],
                ['nom' => 'Louvre', 'rue' => 'Rue de Rivoli', 'lat' => 48.860611, 'lon' => 2.337644],
                ['nom' => 'Notre-Dame', 'rue' => '6 Parvis Notre-Dame', 'lat' => 48.853, 'lon' => 2.3499],
                ['nom' => 'Arc de Triomphe', 'rue' => 'Pl. Charles de Gaulle', 'lat' => 48.8738, 'lon' => 2.295],
                ['nom' => 'Sacré-Cœur', 'rue' => '35 Rue du Chevalier de la Barre', 'lat' => 48.8867, 'lon' => 2.3431]
            ],
            'Lyon' => [
                ['nom' => 'Basilique de Fourvière', 'rue' => '8 Pl. de Fourvière', 'lat' => 45.7625, 'lon' => 4.8224],
                ['nom' => 'Parc de la Tête d\'Or', 'rue' => 'Boulevard des Belges', 'lat' => 45.784, 'lon' => 4.858],
                ['nom' => 'Vieux Lyon', 'rue' => 'Quartier Historique', 'lat' => 45.764, 'lon' => 4.827],
                ['nom' => 'Place Bellecour', 'rue' => 'Pl. Bellecour', 'lat' => 45.7578, 'lon' => 4.832],
                ['nom' => 'Musée des Confluences', 'rue' => '86 Quai Perrache', 'lat' => 45.733, 'lon' => 4.819]
            ],
            'Marseille' => [
                ['nom' => 'Vieux-Port', 'rue' => 'Quai du Port', 'lat' => 43.2965, 'lon' => 5.3698],
                ['nom' => 'Basilique Notre-Dame de la Garde', 'rue' => 'Rue Fort du Sanctuaire', 'lat' => 43.285, 'lon' => 5.371],
                ['nom' => 'Calanques de Marseille', 'rue' => 'Parc National des Calanques', 'lat' => 43.214, 'lon' => 5.435],
                ['nom' => 'Fort Saint-Jean', 'rue' => 'Prom. Louis Brauquier', 'lat' => 43.296, 'lon' => 5.361],
                ['nom' => 'Palais Longchamp', 'rue' => 'Bd Jardin Zoologique', 'lat' => 43.304, 'lon' => 5.396]
            ],
            'Toulouse' => [
                ['nom' => 'Place du Capitole', 'rue' => 'Pl. du Capitole', 'lat' => 43.6043, 'lon' => 1.4437],
                ['nom' => 'Basilique Saint-Sernin', 'rue' => 'Pl. Saint-Sernin', 'lat' => 43.6095, 'lon' => 1.442],
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
                ['nom' => 'Petite France', 'rue' => 'Quartier Petite France', 'lat' => 48.5794, 'lon' => 7.7396],
                ['nom' => 'Palais Rohan', 'rue' => '2 Pl. du Château', 'lat' => 48.5815, 'lon' => 7.7515],
                ['nom' => 'Parc de l\'Orangerie', 'rue' => 'Allée de la Robertsau', 'lat' => 48.5964, 'lon' => 7.7766],
                ['nom' => 'Barrages Vauban', 'rue' => 'Pl. du Quartier Blanc', 'lat' => 48.5791, 'lon' => 7.7378]
            ],
            'Nantes' => [
                ['nom' => 'Château des Ducs de Bretagne', 'rue' => '4 Pl. Marc Elder', 'lat' => 47.216, 'lon' => -1.552],
                ['nom' => 'Les Machines de l\'Île', 'rue' => 'Bd Léon Bureau', 'lat' => 47.206, 'lon' => -1.562],
                ['nom' => 'Passage Pommeraye', 'rue' => 'Passage Pommeraye', 'lat' => 47.215, 'lon' => -1.556],
                ['nom' => 'Cathédrale Saint-Pierre-et-Saint-Paul', 'rue' => 'Pl. Saint-Pierre', 'lat' => 47.218, 'lon' => -1.552],
                ['nom' => 'Île de Versailles', 'rue' => 'Île de Versailles', 'lat' => 47.225, 'lon' => -1.548]
            ],
            'Montpellier' => [
                ['nom' => 'Place de la Comédie', 'rue' => 'Pl. de la Comédie', 'lat' => 43.6086, 'lon' => 3.8792],
                ['nom' => 'Jardin des Plantes', 'rue' => 'Blvd Henri IV', 'lat' => 43.6136, 'lon' => 3.8737],
                ['nom' => 'Promenade du Peyrou', 'rue' => 'Rue Foch', 'lat' => 43.611, 'lon' => 3.871],
                ['nom' => 'Arc de Triomphe', 'rue' => 'Rue Foch', 'lat' => 43.6116, 'lon' => 3.8707],
                ['nom' => 'Esplanade Charles-de-Gaulle', 'rue' => 'Esplanade Charles-de-Gaulle', 'lat' => 43.6097, 'lon' => 3.882]
            ]];


        $villesEntities = [];

        foreach ($villes as $villeData) {
            $ville = new Ville();
            $ville->setNom($villeData['nom']);
            $ville->setCodePostal($villeData['codePostal']);
            $manager->persist($ville);
            $villesEntities[$villeData['nom']] = $ville;
        }

        foreach ($lieuxParVille as $nomVille => $lieux) {
            if (!isset($villesEntities[$nomVille])) {
                continue;
            }
            $ville = $villesEntities[$nomVille];

            foreach ($lieux as $lieuData) {
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
