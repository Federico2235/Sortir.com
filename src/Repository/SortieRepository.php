<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Sortie::class);
        $this->em = $em;
    }

    public function createSortie(Sortie $sortie, Participant $organisateur): void
    {
        $sortie->setOrganisateur($organisateur);
        $sortie->setSite($organisateur->getSite());

        $etat = $this->em->getRepository(Etat::class)->findOneBy(['libelle' => 'Créée']);
        $sortie->setEtat($etat);

        $this->em->persist($sortie);
        $this->em->flush();
    }

    public function getSortieDetails(int $id): ?Sortie
    {
        $sortie = $this->find($id);

        if (!$sortie || $sortie->getId() === null) {
            return null;
        }

        if ($sortie->getDateHeureDebut() < (new DateTime())->sub(new DateInterval('P30D'))) {
            return null;
        }

        return $sortie;
    }

    public function inscrireParticipant(Sortie $sortie, Participant $participant): bool
    {
        if ($sortie->getParticipants()->contains($participant)) {
            return false;
        }

        if ($sortie->getEtat()->getLibelle() == 'Annulée') {
            return false;
        }

        if ($sortie->getNbInscriptionsMax() <= count($sortie->getParticipants())) {
            return false;
        }

        if ($sortie->getDateLimiteInscription() < new DateTimeImmutable()) {
            return false;
        }

    //    public function findOneBySomeField($value): ?SortieFixtures
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function save(Sortie $sortie, bool $flush = true): void
    {
        $em = $this->getEntityManager();

        $em->persist($sortie);

        if ($flush) {
            $em->flush();
        }
    }
}
        if ($sortie->getEtat()->getLibelle() == 'Ouverte') {
            $sortie->addParticipant($participant);
            $this->em->flush();
            return true;
        }

        return false;
    }

    public function desinscrireParticipant(Sortie $sortie, Participant $participant): void
    {
        $sortie->removeParticipant($participant);
        $this->em->flush();
    }

    public function annulerSortie(Sortie $sortie, string $motif, Participant $admin): bool
    {
        $now = new DateTime();

        if ($sortie->getDateHeureDebut() <= $now) {
            return false;
        }

        $etatAnnule = $this->em->getRepository(Etat::class)->findOneBy(['libelle' => 'Annulée']);
        $sortie->setEtat($etatAnnule);
        $sortie->setInfosSortie($sortie->getInfosSortie() . '<br><span style="color: red; font-weight: bold;">(Annulation!!: ' . $motif . ' par : ' . $admin->getNom() . ' ' . $admin->getPrenom() . ')</span>');
        $this->em->flush();

        return true;
    }

    public function publierSortie(Sortie $sortie): void
    {
        $etatPublie = $this->em->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
        $sortie->setEtat($etatPublie);
        $this->em->flush();
    }

    public function deleteSortie(Sortie $sortie): void
    {
        $this->em->remove($sortie);
        $this->em->flush();
    }

    public function getVillesAndLieux(): array
    {
        return [
            'villes' => $this->em->getRepository(Ville::class)->findAll(),
            'lieux' => $this->em->getRepository(Lieu::class)->findAll()
        ];
    }
}