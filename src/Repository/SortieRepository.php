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
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

        $this->save($sortie);
    }

    public function getSortieDetails(int $id): ?Sortie
    {
        try {
            $sortie = $this->createQueryBuilder('s')
                ->addSelect('o', 'site', 'e', 'l', 'v', 'p')
                ->leftJoin('s.organisateur', 'o')
                ->leftJoin('s.site', 'site')
                ->leftJoin('s.etat', 'e')
                ->leftJoin('s.lieu', 'l')
                ->leftJoin('l.ville', 'v')
                ->leftJoin('s.participants', 'p')
                ->where('s.id = :id')
                ->andWhere('s.dateHeureDebut >= :dateLimit')
                ->setParameter('id', $id)
                ->setParameter('dateLimit', (new DateTime())->sub(new DateInterval('P30D')))
                ->getQuery()
                ->getOneOrNullResult();

            return $sortie instanceof Sortie ? $sortie : null;
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function inscrireParticipant(Sortie $sortie, Participant $participant): bool
    {
        if ($sortie->getParticipants()->contains($participant)) {
            return false;
        }
        // Vérification rapide avec COUNT pour éviter de charger tous les participants
        try {
            $nbParticipants = $this->em->createQueryBuilder()
                ->select('COUNT(p.id)')
                ->from(Participant::class, 'p')
                ->join('p.sorties', 's')
                ->where('s.id = :sortieId')
                ->setParameter('sortieId', $sortie->getId())
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }

        $conditions = [
            $sortie->getParticipants()->contains($participant),
            $sortie->getEtat()->getLibelle() === 'Annulée',
            $sortie->getNbInscriptionsMax() <= $nbParticipants,
            $sortie->getDateLimiteInscription() < new DateTimeImmutable(),
            $sortie->getEtat()->getLibelle() !== 'Ouverte',
        ];

        if (in_array(true, $conditions, true)) {
            return false;
        };

        $sortie->addParticipant($participant);
        $this->em->flush();
        return true;
    }

    public function desinscrireParticipant(Sortie $sortie, Participant $participant): void
    {
        $sortie->removeParticipant($participant);
        $this->em->flush();

    }

    public function annulerSortie(Sortie $sortie, string $motif, Participant $admin): bool
    {
        if ($sortie->getDateHeureDebut() <= new DateTime()) {
            return false;
        }

        // Requête DQL optimisée pour la mise à jour
        $updated = $this->em->createQuery('
            UPDATE App\Entity\Sortie s
            SET s.etat = (
                SELECT e.id FROM App\Entity\Etat e WHERE e.libelle = :etatAnnule
            ),
            s.infosSortie = CONCAT(s.infosSortie, :motif)
            WHERE s.id = :id
            AND s.dateHeureDebut > CURRENT_TIMESTAMP()
        ')
            ->setParameters([
                'etatAnnule' => 'Annulée',
                'motif' => '<br><span style="color: red; font-weight: bold;">(Annulation!!: '
                    . $motif . ' par : ' . $admin->getNom() . ' ' . $admin->getPrenom() . ')</span>',
                'id' => $sortie->getId()
            ])
            ->execute();

        return $updated > 0;
    }

    public function publierSortie(Sortie $sortie): void
    {
        // Requête optimisée pour la publication
        $this->em->createQuery('
            UPDATE App\Entity\Sortie s
            SET s.etat = (
                SELECT e.id FROM App\Entity\Etat e WHERE e.libelle = :etatPublie
            )
            WHERE s.id = :id
        ')
            ->setParameters([
                'etatPublie' => 'Ouverte',
                'id' => $sortie->getId()
            ])
            ->execute();
    }

    public function deleteSortie(Sortie $sortie): void
    {
        // Suppression en cascade plus efficace
        $this->em->createQuery('
            DELETE FROM App\Entity\Sortie s WHERE s.id = :id
        ')
            ->setParameter('id', $sortie->getId())
            ->execute();
    }

    public function getVillesWithLieux(): array
    {
        return $this->em->createQueryBuilder()
            ->select('v', 'l')
            ->from(Ville::class, 'v')
            ->leftJoin('v.lieux', 'l')
            ->orderBy('v.nom', 'ASC')
            ->addOrderBy('l.nom', 'ASC')
            ->getQuery()
            ->getResult();
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

    public function sortieFilters(?array $filters, ?Participant $user): array
    {
        // Création du builder de la requête qui va assembler les différents filtres
        $qb = $this->createQueryBuilder('s')
            ->where('s.dateHeureDebut >= :dateLimite')
            ->setParameter('dateLimite', new \DateTime('-1 month'));

        // Sélecteur prposeant les différents sites
        if (!empty($filters['site'])) {
            $qb->andWhere('s.site = :site')
                ->setParameter('site', $filters['site']);
        }

        // Barre de recherche par mot
        if (!empty($filters['nom'])) {
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $filters['nom'] . '%');
        }

        // INTERVAL
        //// Sélecteur début de l'interval
        if (!empty($filters['dateDebut'])) {
            $qb->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $filters['dateDebut']);
        }

        //// Sélecteur fin de l'interval
        if (!empty($filters['dateFin'])) {
            $qb->andWhere('s.dateHeureDebut <= :dateFin')
                ->setParameter('dateFin', $filters['dateFin']);
        }

        // CHECKLIST
        //// Checkpoint Utilisateur est organisateur
        if (!empty($filters['organisateur'])) {
            $qb->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $user);
        }

        //// Checkpoint Utilisateur est inscrit
        if (!empty($filters['inscrit'])) {
            $qb->andWhere(':user MEMBER OF s.participants')
                ->setParameter('user', $user);
        }

        //// Checkpoint Utilisateur n'est organisateur
        if (!empty($filters['nonInscrit'])) {
            $qb->andWhere(':user NOT MEMBER OF s.participants')
                ->setParameter('user', $user);
        }

        //// Checkpoint les Sorties sont passées
        if (!empty($filters['terminee'])) {
            // Faire une jointure explicite avec l'entité Etat
            $qb->join('s.etat', 'e')  // 'e' est l'alias pour l'entité Etat
            ->andWhere('e.libelle = :terminee')
                ->setParameter('terminee', 'Terminée');
        }

        // Affichage des sorties correspondant aux critères sélectionnés
        return $qb->getQuery()->getResult();
    }

    public function save(Sortie $sortie, bool $flush = true): void
    {
        $this->em->persist($sortie);

        if ($flush) {
            $this->em->flush();
        }
    }

    public function findSortiesAMettreAJourEtat(\DateTimeImmutable $now)
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.etat', 'e')
            ->leftJoin('s.site', 'site')
            ->where('e.libelle IN (:etats)')

            ->orWhere('s.dateLimiteInscription < :now')
            ->orWhere('s.dateHeureDebut < :now')
            ->orWhere('DATE_ADD(s.dateHeureDebut, s.duree, \'minute\') < :now')
            ->orWhere('DATE_ADD(DATE_ADD(s.dateHeureDebut, s.duree, \'minute\'), 30, \'day\') < :now')

            ->setParameter('now', $now)
            ->setParameter('etats', ['Cloturée', 'Activité en cours', 'Terminée', 'Historisée']);

        return $qb->getQuery()->getResult();
    }
}