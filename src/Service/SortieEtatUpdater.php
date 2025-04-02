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
    )
    {}

    public function updateEtats(): void
    {
        $now = new DateTimeImmutable('now');
        $etatsLibelles = ['Cloturée', 'Activité en cours', 'Terminée', 'Historisée'];
        $etats = $this->etatRepository->findBy(['libelle' => $etatsLibelles]);

        $etatMap = [];
        foreach ($etats as $etat) {
            $etatMap[$etat->getLibelle()] = $etat;
        }

        $sorties = $this->sortieRepository->findSortiesAMettreAJourEtat($now);

        foreach ($sorties as $sortie) {
            $etatActuel = $sortie->getEtat();
            $dateLimite = $sortie->getDateLimiteInscription();
            $dateDebut = $sortie->getDateHeureDebut();
            $dateFin = $dateDebut->modify("+{$sortie->getDuree()} minutes");
            $dateHistorisation = $dateFin->modify('+30 days');

            if (in_array($etatActuel->getLibelle(), ['Annulée', 'Créée'], true)) {
                continue;
            }

            $nouvelEtat = null;
            if ($dateLimite < $now && $now < $dateDebut) {
                $nouvelEtat = $etatMap['Cloturée'] ?? null;
            } elseif ($dateDebut < $now && $now < $dateFin) {
                $nouvelEtat = $etatMap['Activité en cours'] ?? null;
            } elseif ($dateFin < $now && $now < $dateHistorisation) {
                $nouvelEtat = $etatMap['Terminée'] ?? null;
            }

            if ($dateHistorisation < $now) {
                $nouvelEtat = $etatMap['Historisée'] ?? null;
            }

            if ($nouvelEtat && $nouvelEtat !== $etatActuel) {
                $sortie->setEtat($nouvelEtat);
                $this->sortieRepository->save($sortie, true);
            }
        }
    }
}