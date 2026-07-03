<?php

/**
 * public/test.php
 * Script de test rapide pour valider chaque couche independamment
 * de l'interface utilisateur. A lancer en CLI :
 *   php public/test.php
 * ou via navigateur : http://localhost/fitconnect/public/test.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor_autoload.php';

use Config\Database;
use App\Entities\Adherent;
use App\Repositories\AdherentRepository;
use App\Repositories\AbonnementRepository;
use App\Repositories\SeanceRepository;
use App\Services\AdherentService;
use App\Services\AbonnementService;
use App\Services\SeanceService;

header('Content-Type: text/plain; charset=utf-8');

function section(string $title): void
{
    echo "\n==== {$title} ====\n";
}

function check(string $label, bool $condition): void
{
    echo ($condition ? '[OK]   ' : '[FAIL] ') . $label . PHP_EOL;
}

try {
    $pdo = Database::getConnection();
    section('Connexion PDO');
    check('Connexion etablie', $pdo instanceof PDO);

    // ---------------------------------------------------------
    // ENTITES
    // ---------------------------------------------------------
    section('Entites');
    $adherentTest = new Adherent('Testeur', 'Alpha', 'testeur.alpha@example.com', '0600000099', date('Y-m-d'), 1);
    check('Entite Adherent instanciee', $adherentTest->getNomComplet() === 'Alpha Testeur');

    try {
        new \App\Entities\Abonnement(1, 'invalide', date('Y-m-d'), date('Y-m-d', strtotime('+1 month')));
        check('Rejet type abonnement invalide', false);
    } catch (\InvalidArgumentException $e) {
        check('Rejet type abonnement invalide', true);
    }

    // ---------------------------------------------------------
    // REPOSITORIES
    // ---------------------------------------------------------
    section('Repositories');
    $adherentRepo = new AdherentRepository($pdo);
    $abonnementRepo = new AbonnementRepository($pdo);
    $seanceRepo = new SeanceRepository($pdo);

    $tousAdherents = $adherentRepo->findAll();
    check('AdherentRepository::findAll retourne des resultats', count($tousAdherents) > 0);

    $tousAbonnements = $abonnementRepo->findAll();
    check('AbonnementRepository::findAll retourne des resultats', count($tousAbonnements) > 0);

    $toutesSeances = $seanceRepo->findAll();
    check('SeanceRepository::findAll retourne des resultats', count($toutesSeances) > 0);

    // ---------------------------------------------------------
    // SERVICES
    // ---------------------------------------------------------
    section('Services');
    $adherentService = new AdherentService($adherentRepo);
    $abonnementService = new AbonnementService($abonnementRepo);
    $seanceService = new SeanceService($seanceRepo, $abonnementService);

    // Test : inscription d'un nouvel adherent (email unique via timestamp)
    $emailTest = 'test.' . time() . '@example.com';
    $idNouvel = $adherentService->inscrire('Test', 'Unitaire', $emailTest, null, 1);
    check('AdherentService::inscrire cree un adherent', $idNouvel > 0);

    // Test : email deja utilise -> doit lever une exception
    try {
        $adherentService->inscrire('Test', 'Duplicate', $emailTest, null, 1);
        check('AdherentService::inscrire rejette un email duplique', false);
    } catch (\InvalidArgumentException $e) {
        check('AdherentService::inscrire rejette un email duplique', true);
    }

    // Test : souscription abonnement + regle "un seul actif"
    $idAbonnement1 = $abonnementService->souscrire($idNouvel, 'mensuel');
    check('AbonnementService::souscrire cree un abonnement actif', $idAbonnement1 > 0);
    check('AbonnementService::estAbonneValide -> true', $abonnementService->estAbonneValide($idNouvel));

    $idAbonnement2 = $abonnementService->souscrire($idNouvel, 'annuel');
    $actifApres = $abonnementService->abonnementActif($idNouvel);
    check(
        'Un seul abonnement actif a la fois (le precedent est resilie)',
        $actifApres !== null && $actifApres->getIdAbonnement() === $idAbonnement2
    );

    // Test : enregistrement de seance (doit reussir, abonnement valide)
    $idSeance = $seanceService->enregistrer($idNouvel, 1, 1, null, 45);
    check('SeanceService::enregistrer reussit avec abonnement valide', $idSeance > 0);

    // Test : suppression bloquee par regle de gestion (adherent avec seances)
    try {
        $adherentService->supprimer($idNouvel);
        check('AdherentService::supprimer bloque un adherent avec seances', false);
    } catch (\RuntimeException $e) {
        check('AdherentService::supprimer bloque un adherent avec seances', true);
    }

    section('Resume');
    echo "Tests termines.\n";
} catch (\Throwable $e) {
    echo "ERREUR FATALE : " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
