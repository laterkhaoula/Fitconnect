<?php

namespace App\Controllers;

use App\Services\AdherentService;
use App\Repositories\AdherentRepository;
use PDO;

class AdherentController
{
    private AdherentService $adherentService;
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->adherentService = new AdherentService(new AdherentRepository($pdo));
    }

    public function index(): void
    {
        $adherents = $this->adherentService->listerTous();
        $salles = $this->pdo->query('SELECT * FROM salles ORDER BY nom')->fetchAll();
        $erreur = null;
        $succes = null;

        // Suppression
        if (isset($_GET['delete'])) {
            try {
                $this->adherentService->supprimer((int) $_GET['delete']);
                header('Location: index.php?page=adherents&msg=deleted');
                exit;
            } catch (\Throwable $e) {
                $erreur = $e->getMessage();
            }
        }
        if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
            $succes = 'Adherent supprime avec succes.';
        }
        if (isset($_GET['msg']) && $_GET['msg'] === 'created') {
            $succes = 'Adherent cree avec succes.';
        }

        require __DIR__ . '/../../views/adherents/index.php';
    }

    public function create(): void
    {
        $salles = $this->pdo->query('SELECT * FROM salles ORDER BY nom')->fetchAll();
        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->adherentService->inscrire(
                    trim($_POST['nom'] ?? ''),
                    trim($_POST['prenom'] ?? ''),
                    trim($_POST['email'] ?? ''),
                    trim($_POST['telephone'] ?? '') ?: null,
                    (int) ($_POST['id_salle'] ?? 0)
                );
                header('Location: index.php?page=adherents&msg=created');
                exit;
            } catch (\Throwable $e) {
                $erreur = $e->getMessage();
            }
        }

        require __DIR__ . '/../../views/adherents/create.php';
    }
}
