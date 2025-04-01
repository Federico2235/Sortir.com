<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VilleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $villes = [
            ['nom' => 'Rennes', 'codePostal' => '35000'],
            ['nom' => 'Nantes', 'codePostal' => '44000'],
            ['nom' => 'Paris', 'codePostal' => '75000'],
            ['nom' => 'Lyon', 'codePostal' => '69000'],
            ['nom' => 'Bordeaux', 'codePostal' => '33000'],
            ['nom' => 'Marseille', 'codePostal' => '13000'],
            ['nom' => 'Toulouse', 'codePostal' => '31000'],
            ['nom' => 'Lille', 'codePostal' => '59000'],
            ['nom' => 'Strasbourg', 'codePostal' => '67000'],
            ['nom' => 'Nice', 'codePostal' => '06000'],
        ];

        // Crear y persistir las ciudades
        foreach ($villes as $villeData) {
            $ville = new Ville();
            $ville->setNom($villeData['nom']);
            $ville->setCodePostal($villeData['codePostal']);
            $manager->persist($ville);
        }
        $manager->flush();
    }
}
