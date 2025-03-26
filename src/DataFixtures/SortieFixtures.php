<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\This;

class SortieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {


        // $product = new Product();
        // $manager->persist($product);
//Create Sortie with all fields filled

        $ville = new Ville();
        $ville->setNom("ville 1");
        $ville->setCodePostal(1);
        $manager->persist($ville);
        $this->addReference("ville-1", $ville);
        $manager->flush();


        $lieu = new Lieu();
        $lieu->setNom("lieu 1");
        $lieu->setRue("rue 1");
        $lieu->setLatitude(1);
        $lieu->setLongitude(1);
        $lieu->setVille($this->getReference("ville-1",Ville::class));
        $manager->persist($lieu);
        $this->addReference("lieu-1", $lieu);
        $manager->flush();

        $etat= new Etat();
        $etat->setLibelle("Créée");
        $manager->persist($etat);
        $this->addReference("etat-1", $etat);
        $manager->flush();

        $participant = new Participant();
        $participant->setNom("participant 1");
        $participant->setPrenom("participant 1");
        $participant->setTelephone("participant 1");
        $participant->setMail("participant 1");
        $participant->setAdministrateur(true);
        $participant->setActif(true);
        $participant->setPseudo("participant 1");
        $participant->setPassword("participant 1");
        $participant->setRoles(["ROLE_USER"]);
        $manager->persist($participant);
        $this->addReference("participant-1", $participant);
        $manager->flush();

        $site = new Site();
        $site->setNom("site 1");
        $manager->persist($site);
        $this->addReference("site-1", $site);
        $manager->flush();

        $sortie = new Sortie();
        $sortie->setNom("sortie 1");
        $sortie->setDateHeureDebut(new \DateTimeImmutable("2024-05-01"));
        $sortie->setDuree(1);
        $sortie->setLieu($this->getReference("lieu-1",Lieu::class)); //("lieu 1");
        $sortie->setNbInscriptionsMax(10);
        $sortie->setDateLimiteInscription(new \DateTimeImmutable("2024-04-28"));
        $sortie->setInfosSortie("infos 1");
        $sortie->setEtat($this->getReference("etat-1",Etat::class));
        $sortie->setOrganisateur($this->getReference("participant-1",Participant::class));
        $sortie->setSite($this->getReference("site-1",Site::class));


        $manager->persist($sortie);


        $manager->flush();
    }
}
