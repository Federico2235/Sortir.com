<?php

namespace App\DataFixtures;

use AllowDynamicProperties;
use App\Entity\Participant;
use App\Entity\Site;
use App\Repository\SiteRepository;
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
        $site = $manager->getRepository(Site::class)->find(9);

        if (!$site) {
            throw new \Exception("El sitio con ID 5 no existe.");
        }



        $participant = new Participant();
        $participant->setNom('Doe');
        $participant->setPrenom('John');
        $participant->setTelephone('123456789');
        $participant->setMail('johndoe@example.com');
        $participant->setAdministrateur(false);
        $participant->setActif(true);
        $participant->setPseudo('johndoe');
        $participant->setSite($site);

        // Encriptar el password
        $hashedPassword = $this->passwordHasher->hashPassword($participant, '123456');
        $participant->setPassword($hashedPassword);

        // Establecer roles
        $participant->setRoles(['ROLE_USER']);

        // Persistir la entidad en la base de datos
        $manager->persist($participant);

        // Hacer flush para guardar en la base de datos
        $manager->flush();

    }
}
