<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SiteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $site1 = new Site();
        $site1->setNom('Rennes');
        $manager->persist($site1);


        $site2 = new Site();
        $site2->setNom('Paris');
        $manager->persist($site2);


        $site3 = new Site();
        $site3->setNom('Lyon');
        $manager->persist($site3);


        $manager->flush();

    }
}
