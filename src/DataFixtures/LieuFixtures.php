<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $lieuxParis = [
            ['nom' => 'Tour Eiffel', 'rue' => 'Av. Gustave Eiffel', 'lat' => 48.858370, 'lon' => 2.294480],
            ['nom' => 'Louvre', 'rue' => 'Rue de Rivoli', 'lat' => 48.860611, 'lon' => 2.337644],
            ['nom' => 'Notre-Dame', 'rue' => '6 Parvis Notre-Dame', 'lat' => 48.853, 'lon' => 2.3499],
            ['nom' => 'Arc de Triomphe', 'rue' => 'Pl. Charles de Gaulle', 'lat' => 48.8738, 'lon' => 2.295],
            ['nom' => 'Sacré-Cœur', 'rue' => '35 Rue du Chevalier de la Barre', 'lat' => 48.8867, 'lon' => 2.3431]
        ];

        $lieuxLyon = [
            ['nom' => 'Basilique de Fourvière', 'rue' => '8 Pl. de Fourvière', 'lat' => 45.7625, 'lon' => 4.8224],
            ['nom' => 'Parc de la Tête d\'Or', 'rue' => 'Boulevard des Belges', 'lat' => 45.784, 'lon' => 4.858],
            ['nom' => 'Vieux Lyon', 'rue' => 'Quartier Historique', 'lat' => 45.764, 'lon' => 4.827],
            ['nom' => 'Place Bellecour', 'rue' => 'Pl. Bellecour', 'lat' => 45.7578, 'lon' => 4.832],
            ['nom' => 'Musée des Confluences', 'rue' => '86 Quai Perrache', 'lat' => 45.733, 'lon' => 4.819]
        ];

        $lieuxMarseille = [
            ['nom' => 'Vieux-Port', 'rue' => 'Quai du Port', 'lat' => 43.2965, 'lon' => 5.3698],
            ['nom' => 'Basilique Notre-Dame de la Garde', 'rue' => 'Rue Fort du Sanctuaire', 'lat' => 43.285, 'lon' => 5.371],
            ['nom' => 'Calanques de Marseille', 'rue' => 'Parc National des Calanques', 'lat' => 43.214, 'lon' => 5.435],
            ['nom' => 'Fort Saint-Jean', 'rue' => 'Prom. Louis Brauquier', 'lat' => 43.296, 'lon' => 5.361],
            ['nom' => 'Palais Longchamp', 'rue' => 'Bd Jardin Zoologique', 'lat' => 43.304, 'lon' => 5.396]
        ];

        $lieuxToulouse = [
            ['nom' => 'Place du Capitole', 'rue' => 'Pl. du Capitole', 'lat' => 43.6043, 'lon' => 1.4437],
            ['nom' => 'Basilique Saint-Sernin', 'rue' => 'Pl. Saint-Sernin', 'lat' => 43.6095, 'lon' => 1.442],
            ['nom' => 'Cité de l\'Espace', 'rue' => 'Avenue Jean Gonord', 'lat' => 43.5866, 'lon' => 1.4825],
            ['nom' => 'Pont Neuf', 'rue' => 'Pont Neuf', 'lat' => 43.5995, 'lon' => 1.4394],
            ['nom' => 'Jardin des Plantes', 'rue' => 'Allée Jules Guesde', 'lat' => 43.5935, 'lon' => 1.451]
        ];

        $lieuxBordeaux = [
            ['nom' => 'Place de la Bourse', 'rue' => 'Pl. de la Bourse', 'lat' => 44.8412, 'lon' => -0.5691],
            ['nom' => 'Cité du Vin', 'rue' => '134 Quai de Bacalan', 'lat' => 44.8625, 'lon' => -0.5502],
            ['nom' => 'Pont de Pierre', 'rue' => 'Pont de Pierre', 'lat' => 44.8379, 'lon' => -0.5655],
            ['nom' => 'Parc Bordelais', 'rue' => 'Rue du Bocage', 'lat' => 44.8522, 'lon' => -0.5993],
            ['nom' => 'Cathédrale Saint-André', 'rue' => 'Pl. Pey Berland', 'lat' => 44.837, 'lon' => -0.576]
        ];

        $lieuxLille = [
            ['nom' => 'Grand-Place', 'rue' => 'Pl. du Général de Gaulle', 'lat' => 50.636, 'lon' => 3.063],
            ['nom' => 'Palais des Beaux-Arts', 'rue' => 'Pl. de la République', 'lat' => 50.6324, 'lon' => 3.0612],
            ['nom' => 'Vieille Bourse', 'rue' => 'Pl. du Théâtre', 'lat' => 50.6364, 'lon' => 3.0632],
            ['nom' => 'Zoo de Lille', 'rue' => 'Avenue Mathias Delobel', 'lat' => 50.6383, 'lon' => 3.0515],
            ['nom' => 'Citadelle de Lille', 'rue' => 'Av. du 43e Régiment d\'Infanterie', 'lat' => 50.6401, 'lon' => 3.0479]
        ];

        $lieuxNice = [
            ['nom' => 'Promenade des Anglais', 'rue' => 'Promenade des Anglais', 'lat' => 43.695, 'lon' => 7.265],
            ['nom' => 'Colline du Château', 'rue' => 'Rue des Ponchettes', 'lat' => 43.692, 'lon' => 7.281],
            ['nom' => 'Place Masséna', 'rue' => 'Pl. Masséna', 'lat' => 43.6973, 'lon' => 7.2709],
            ['nom' => 'Musée Matisse', 'rue' => '164 Avenue des Arènes de Cimiez', 'lat' => 43.7206, 'lon' => 7.276],
            ['nom' => 'Marché aux Fleurs', 'rue' => 'Cours Saleya', 'lat' => 43.6955, 'lon' => 7.273]
        ];

        $manager->flush();
    }
}
