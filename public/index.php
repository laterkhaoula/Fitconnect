<?php

/**
 * Point d'entree unique de l'application FitConnect.
 * Routeur minimaliste base sur les parametres GET `page` et `action`.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor_autoload.php';

use Config\Database;
use App\Controllers\AdherentController;
use App\Controllers\AbonnementController;
use App\Controllers\SeanceController;

try {
    $pdo = Database::getConnection();
} catch (\Throwable $e) {
    http_response_code(500);
    echo '<h1>Erreur de connexion a la base de donnees</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
}

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

switch ($page) {
    case 'adherents':
        $controller = new AdherentController($pdo);
        $action === 'create' ? $controller->create() : $controller->index();
        break;

    case 'abonnements':
        $controller = new AbonnementController($pdo);
        $action === 'create' ? $controller->create() : $controller->index();
        break;

    case 'dashboard':
    default:
        (new SeanceController($pdo))->dashboard();
        break;
}
