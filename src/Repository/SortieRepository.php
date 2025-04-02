<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    //    /**
    //     * @return SortieFixtures[] Returns an array of SortieFixtures objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

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
        if (!empty($filters['passee'])) {
            $qb->andWhere('s.dateHeureDebut < :now')
                ->setParameter('now', new \DateTime());
        }

        // Affichage des sorties correspondant aux critères sélectionnés
        return $qb->getQuery()->getResult();
    }

    public function save(Sortie $sortie, bool $flush = true): void
    {
        $em = $this->getEntityManager();

        $em->persist($sortie);

        if ($flush) {
            $em->flush();
        }
    }

    public function findSortiesAMettreAJourEtat(\DateTimeImmutable $now)
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.etat', 'e')
            ->where('e.libelle IN (:etats)')
            ->orWhere('s.dateLimiteInscription < :now')
            ->orWhere('s.dateHeureDebut < :now')
            ->orWhere('DATE_ADD(s.dateHeureDebut, s.duree, \'minute\') < :now')
            ->orWhere('DATE_ADD(DATE_ADD(s.dateHeureDebut, s.duree, \'minute\'), 30, \'day\') < :now')
            ->setParameter('now', $now)
            ->setParameter('etats', ['Cloturée', 'Activité en cours', 'Terminée', 'Historisée'])
            ->getQuery()
            ->getResult();

        return $qb;
    }
}
