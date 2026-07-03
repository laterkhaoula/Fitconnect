<?php

namespace App\Controllers;

use App\Services\SeanceService;
use App\Services\AbonnementService;
use App\Repositories\SeanceRepository;
use App\Repositories\AbonnementRepository;
use App\Repositories\AdherentRepository;
use PDO;

class SeanceController
{
    private SeanceService $seanceService;
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $abonnementService = new AbonnementService(new AbonnementRepository($pdo));
        $this->seanceService = new SeanceService(new SeanceRepository($pdo), $abonnementService);
    }

    /** Tableau de bord : liste des seances + formulaire rapide d'enregistrement. */
    public function dashboard(): void
    {
        $seances = $this->seanceService->listerDetaillees();
        $adherents = (new AdherentRepository($this->pdo))->findAll();
        $salles = $this->pdo->query('SELECT * FROM salles ORDER BY nom')->fetchAll();
        $activites = $this->pdo->query('SELECT * FROM activites ORDER BY nom')->fetchAll();
        $equipements = $this->pdo->query('SELECT * FROM equipements ORDER BY nom')->fetchAll();
        $erreur = null;
        $succes = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->seanceService->enregistrer(
                    (int) ($_POST['id_adherent'] ?? 0),
                    (int) ($_POST['id_salle'] ?? 0),
                    (int) ($_POST['id_activite'] ?? 0),
                    !empty($_POST['id_equipement']) ? (int) $_POST['id_equipement'] : null,
                    (int) ($_POST['duree_minutes'] ?? 0)
                );
                $succes = 'Seance enregistree avec succes.';
                $seances = $this->seanceService->listerDetaillees();
            } catch (\Throwable $e) {
                $erreur = $e->getMessage();
            }
        }

        require __DIR__ . '/../../views/dashboard/index.php';
    }
}
