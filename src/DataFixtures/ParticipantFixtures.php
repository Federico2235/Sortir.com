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


        $participant = new Participant();
        $participant->setNom('Doe');
        $participant->setPrenom('John');
        $participant->setTelephone('1234567891');
        $participant->setMail('johndoe@example.com');
        $participant->setAdministrateur(false);
        $participant->setActif(true);
        $participant->setPseudo('johndoe');
        $participant->setRoles(['ROLE_USER']);
        $participant->setPassword($this->passwordHasher->hashPassword($participant, '123456'));
        $participant->setPhoto('avatar.jpg');

        $participant->setSite($site1);
        $manager->persist($participant);
        $this->addReference('participant', $participant);

        $participant2 = new Participant();
        $participant2->setNom('Smith');
        $participant2->setPrenom('Emily');
        $participant2->setTelephone('9876543212');
        $participant2->setMail('emilysmith@example.com');
        $participant2->setAdministrateur(false);
        $participant2->setActif(true);
        $participant2->setPseudo('emilysmith123');
        $participant2->setRoles(['ROLE_USER']);
        $participant2->setSite($site2);
        $participant2->setPassword($this->passwordHasher->hashPassword($participant2, '123456'));
        $participant2->setPhoto('avatar.jpg');

        $manager->persist($participant2);
        $this->addReference('participant_2', $participant2);

        $participant3 = new Participant();
        $participant3->setNom('Martin');
        $participant3->setPrenom('Louis');
        $participant3->setTelephone('5551234565');
        $participant3->setMail('louismartin@example.com');
        $participant3->setAdministrateur(true);
        $participant3->setPseudo('louis_martin');
        $participant3->setSite($site3);
        $participant3->setPassword($this->passwordHasher->hashPassword($participant3, '123456'));
        $participant3->setPhoto('avatar.jpg');
        $participant3->setActif(true);

        $participant3->setRoles(['ROLE_ADMIN']);
        $manager->persist($participant3);
        $this->addReference('participant_3', $participant3);

        $manager->flush();
    }

}
