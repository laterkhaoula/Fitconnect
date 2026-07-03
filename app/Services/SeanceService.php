<?php

namespace App\Services;

use App\Entities\Seance;
use App\Repositories\SeanceRepository;
use RuntimeException;

class SeanceService
{
    private SeanceRepository $seanceRepository;
    private AbonnementService $abonnementService;

    public function __construct(SeanceRepository $seanceRepository, AbonnementService $abonnementService)
    {
        $this->seanceRepository = $seanceRepository;
        $this->abonnementService = $abonnementService;
    }

    public function listerToutes(): array
    {
        return $this->seanceRepository->findAll();
    }

    public function listerDetaillees(): array
    {
        return $this->seanceRepository->findAllDetaille();
    }

    public function historiqueAdherent(int $idAdherent): array
    {
        return $this->seanceRepository->findByAdherent($idAdherent);
    }

    /**
     * Regle de gestion centrale : une seance ne peut etre enregistree que
     * si l'abonnement de l'adherent est valide a la date du jour.
     */
    public function enregistrer(
        int $idAdherent,
        int $idSalle,
        int $idActivite,
        ?int $idEquipement,
        int $dureeMinutes,
        ?string $dateSeance = null
    ): int {
        $dateSeance = $dateSeance ?? date('Y-m-d H:i:s');
        $dateJour = substr($dateSeance, 0, 10);

        if (!$this->abonnementService->estAbonneValide($idAdherent, $dateJour)) {
            throw new RuntimeException(
                "Impossible d'enregistrer la seance : aucun abonnement valide pour cet adherent a cette date."
            );
        }

        $seance = new Seance($idAdherent, $idSalle, $idActivite, $idEquipement, $dureeMinutes, $dateSeance);
        return $this->seanceRepository->create($seance);
    }
}
