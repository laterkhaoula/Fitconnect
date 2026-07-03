<?php

namespace App\Controllers;

use App\Services\AbonnementService;
use App\Repositories\AbonnementRepository;
use App\Repositories\AdherentRepository;
use PDO;

class AbonnementController
{
    private AbonnementService $abonnementService;
    private AdherentRepository $adherentRepository;
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->abonnementService = new AbonnementService(new AbonnementRepository($pdo));
        $this->adherentRepository = new AdherentRepository($pdo);
    }

    public function index(): void
    {
        $abonnements = $this->abonnementService->listerTous();
        $adherents = $this->adherentRepository->findAll();
        $succes = isset($_GET['msg']) && $_GET['msg'] === 'created'
            ? 'Abonnement souscrit avec succes.'
            : null;

        require __DIR__ . '/../../views/abonnements/index.php';
    }

    public function create(): void
    {
        $adherents = $this->adherentRepository->findAll();
        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->abonnementService->souscrire(
                    (int) ($_POST['id_adherent'] ?? 0),
                    $_POST['type'] ?? '',
                    trim($_POST['date_debut'] ?? '') ?: null
                );
                header('Location: index.php?page=abonnements&msg=created');
                exit;
            } catch (\Throwable $e) {
                $erreur = $e->getMessage();
            }
        }

        require __DIR__ . '/../../views/abonnements/create.php';
    }
}
