<?php

namespace App\Service;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class SortieEtatUpdater
{
    public function __construct(
        private SortieRepository $sortieRepository,
        private EtatRepository $etatRepository,
        private EntityManagerInterface $em
    )
    {}

    public function updateEtats(): void
    {
        // Récupération de la date actuelle
        $now = new DateTimeImmutable('now');

        // Récupération des états groupés en une seule requête
        $etatsLibelles = ['Cloturée', 'Activité en cours', 'Terminée', 'Historisée'];
        $etats = $this->etatRepository->findBy(['libelle' => $etatsLibelles]);

        // Transformation du tableau d'états en un tableau associatif pour un accès rapide
        $etatMap = [];
        foreach ($etats as $etat) {
            $etatMap[$etat->getLibelle()] = $etat;
        }

        // Récupération des sorties
        $sorties = $this->sortieRepository->findSortiesAMettreAJourEtat($now);

        // Vérification du statut de la sortie (Créée, Ouverte, Cloturée, Activité en cours, Passée, Annulée)
        foreach ($sorties as $sortie) {
            $etatActuel = $sortie->getEtat();

            // Définition des dates
            $dateLimite = $sortie->getDateLimiteInscription();
            $dateDebut = $sortie->getDateHeureDebut();
            $dateFin = $dateDebut->modify("+{$sortie->getDuree()} minutes");
            $dateHistorisation = $dateFin->modify('+30 days');

            $nouvelEtat = null;

            if (!in_array($etatActuel->getLibelle(), ['Annulée', 'Créée'], true)) {
                if ($dateLimite < $now && $now < $dateDebut) {
                    $nouvelEtat = $etatMap['Cloturée'] ?? null;
                } elseif ($dateDebut < $now && $now < $dateFin) {
                    $nouvelEtat = $etatMap['Activité en cours'] ?? null;
                } elseif ($dateFin < $now && $now < $dateHistorisation) {
                    $nouvelEtat = $etatMap['Terminée'] ?? null;
                }
            }

            if ($dateHistorisation < $now) {
                $nouvelEtat = $etatMap['Historisée'] ?? null;
            }

            // Mise à jour si nécessaire
            if ($nouvelEtat && $nouvelEtat !== $etatActuel) {
                $sortie->setEtat($nouvelEtat);
                $this->sortieRepository->save($sortie, true);
            }
        }
    }
}