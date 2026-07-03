<?php

namespace App\Services;

use App\Entities\Abonnement;
use App\Repositories\AbonnementRepository;
use RuntimeException;

class AbonnementService
{
    private AbonnementRepository $abonnementRepository;

    private const DUREE_JOURS = [
        'mensuel'     => 30,
        'trimestriel' => 90,
        'annuel'      => 365,
    ];

    public function __construct(AbonnementRepository $abonnementRepository)
    {
        $this->abonnementRepository = $abonnementRepository;
    }

    public function listerTous(): array
    {
        return $this->abonnementRepository->findAll();
    }

    public function historiqueAdherent(int $idAdherent): array
    {
        return $this->abonnementRepository->findByAdherent($idAdherent);
    }

    public function abonnementActif(int $idAdherent): ?Abonnement
    {
        return $this->abonnementRepository->findActifByAdherent($idAdherent);
    }

    /**
     * Regle de gestion : un adherent ne detient qu'un seul abonnement
     * actif a la fois. On resilie automatiquement l'ancien avant de
     * creer le nouveau (le service reste seul responsable de cette regle,
     * la base ne pouvant pas exprimer une contrainte d'unicite "filtree").
     */
    public function souscrire(int $idAdherent, string $type, ?string $dateDebut = null): int
    {
        if (!array_key_exists($type, self::DUREE_JOURS)) {
            throw new RuntimeException("Type d'abonnement invalide : {$type}");
        }

        $dateDebut = $dateDebut ?? date('Y-m-d');
        $dateFin = date('Y-m-d', strtotime($dateDebut . ' + ' . self::DUREE_JOURS[$type] . ' days'));

        $actif = $this->abonnementRepository->findActifByAdherent($idAdherent);
        if ($actif !== null) {
            $this->abonnementRepository->updateStatut($actif->getIdAbonnement(), 'resilie');
        }

        $abonnement = new Abonnement($idAdherent, $type, $dateDebut, $dateFin, 'actif');
        return $this->abonnementRepository->create($abonnement);
    }

    /** Verifie si l'adherent a un abonnement valide a la date donnee (par defaut aujourd'hui). */
    public function estAbonneValide(int $idAdherent, ?string $date = null): bool
    {
        $actif = $this->abonnementRepository->findActifByAdherent($idAdherent);
        return $actif !== null && $actif->estValideA($date);
    }
}
