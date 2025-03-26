<?php

namespace App\DataFixtures;

use AllowDynamicProperties;
use App\Entity\Participant;
use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AllowDynamicProperties] class ParticipantFixtures extends Fixture
{

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $site1 = new Site();
        $site1->setNom('SAINT HERBLAIN');
        $manager->persist($site1);

        $site2 = new Site();
        $site2->setNom('CHARTRES DE BRETAGNE');
        $manager->persist($site2);

        $site3 = new Site();
        $site3->setNom('LA ROCHE SUR YON');
        $manager->persist($site3);

        // Ahora creamos el 'Participant' y lo asociamos a un 'Site'
        $participant = new Participant();
        $participant->setNom('Doe');
        $participant->setPrenom('John');
        $participant->setTelephone('123456789');
        $participant->setMail('johndoe@example.com');
        $participant->setAdministrateur(false);
        $participant->setActif(true);
        $participant->setPseudo('johndoe');
        $participant->setPassword($this->passwordHasher->hashPassword($participant, '123456'));

        // Asociamos un Site (en este caso, site1)
        $participant->setSite($site1);
        $manager->persist($participant);

        $participant2 = new Participant();
        $participant2->setNom('Smith');
        $participant2->setPrenom('Emily');
        $participant2->setTelephone('987654321');
        $participant2->setMail('emilysmith@example.com');
        $participant2->setAdministrateur(true);
        $participant2->setActif(true);
        $participant2->setPseudo('emilysmith123');
     $participant2->setSite($site2);
     $participant2->setPassword($this->passwordHasher->hashPassword($participant2, 'lalala'));
     $manager->persist($participant2);


        // Tercer participante
        $participant3 = new Participant();
        $participant3->setNom('Martin');
        $participant3->setPrenom('Louis');
        $participant3->setTelephone('555123456');
        $participant3->setMail('louismartin@example.com');
        $participant3->setAdministrateur(false);
        $participant3->setActif(false);
        $participant3->setPseudo('louis_martin');
        $participant3->setSite($site3);
        $participant3->setPassword($this->passwordHasher->hashPassword($participant3, 'tatata'));
        $manager->persist($participant3);

        // Persistimos el participante




        $manager->flush();
    }
}
