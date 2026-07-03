<?php

namespace App\Repositories;

use App\Entities\Adherent;
use PDO;

/**
 * Acces aux donnees de la table `adherents`.
 * Toutes les requetes sont parametrees (protection injection SQL).
 */
class AdherentRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM adherents ORDER BY nom, prenom');
        return array_map(fn($row) => Adherent::fromArray($row), $stmt->fetchAll());
    }

    public function findById(int $id): ?Adherent
    {
        $stmt = $this->pdo->prepare('SELECT * FROM adherents WHERE id_adherent = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Adherent::fromArray($row) : null;
    }

    public function findByEmail(string $email): ?Adherent
    {
        $stmt = $this->pdo->prepare('SELECT * FROM adherents WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? Adherent::fromArray($row) : null;
    }

    public function findBySalle(int $idSalle): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM adherents WHERE id_salle = :id_salle ORDER BY nom');
        $stmt->execute(['id_salle' => $idSalle]);
        return array_map(fn($row) => Adherent::fromArray($row), $stmt->fetchAll());
    }

    public function create(Adherent $adherent): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO adherents (nom, prenom, email, telephone, date_inscription, id_salle)
             VALUES (:nom, :prenom, :email, :telephone, :date_inscription, :id_salle)'
        );
        $stmt->execute([
            'nom'              => $adherent->getNom(),
            'prenom'           => $adherent->getPrenom(),
            'email'            => $adherent->getEmail(),
            'telephone'        => $adherent->getTelephone(),
            'date_inscription' => $adherent->getDateInscription(),
            'id_salle'         => $adherent->getIdSalle(),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(Adherent $adherent): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE adherents
             SET nom = :nom, prenom = :prenom, email = :email,
                 telephone = :telephone, id_salle = :id_salle
             WHERE id_adherent = :id'
        );
        return $stmt->execute([
            'nom'       => $adherent->getNom(),
            'prenom'    => $adherent->getPrenom(),
            'email'     => $adherent->getEmail(),
            'telephone' => $adherent->getTelephone(),
            'id_salle'  => $adherent->getIdSalle(),
            'id'        => $adherent->getIdAdherent(),
        ]);
    }

    /**
     * Supprime un adherent. Grace aux contraintes FK (ON DELETE RESTRICT),
     * MySQL refusera la suppression si des seances ou abonnements existent
     * encore pour cet adherent : on capture ce cas dans la couche Service.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM adherents WHERE id_adherent = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function hasSeances(int $idAdherent): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM seances WHERE id_adherent = :id');
        $stmt->execute(['id' => $idAdherent]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function hasAbonnementEnCours(int $idAdherent): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM abonnements
             WHERE id_adherent = :id AND statut = 'actif'"
        );
        $stmt->execute(['id' => $idAdherent]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
