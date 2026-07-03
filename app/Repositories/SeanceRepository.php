<?php

namespace App\Repositories;

use App\Entities\Seance;
use PDO;

class SeanceRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM seances ORDER BY date_seance DESC');
        return array_map(fn($row) => Seance::fromArray($row), $stmt->fetchAll());
    }

    public function findById(int $id): ?Seance
    {
        $stmt = $this->pdo->prepare('SELECT * FROM seances WHERE id_seance = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Seance::fromArray($row) : null;
    }

    public function findByAdherent(int $idAdherent): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM seances WHERE id_adherent = :id ORDER BY date_seance DESC'
        );
        $stmt->execute(['id' => $idAdherent]);
        return array_map(fn($row) => Seance::fromArray($row), $stmt->fetchAll());
    }

    /** Jointure pour affichage tableau de bord (noms lisibles). */
    public function findAllDetaille(): array
    {
        $sql = "SELECT s.*, a.nom AS adherent_nom, a.prenom AS adherent_prenom,
                       sa.nom AS salle_nom, ac.nom AS activite_nom, e.nom AS equipement_nom
                FROM seances s
                JOIN adherents a ON a.id_adherent = s.id_adherent
                JOIN salles sa ON sa.id_salle = s.id_salle
                JOIN activites ac ON ac.id_activite = s.id_activite
                LEFT JOIN equipements e ON e.id_equipement = s.id_equipement
                ORDER BY s.date_seance DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function create(Seance $seance): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO seances (id_adherent, id_salle, id_activite, id_equipement, duree_minutes, date_seance)
             VALUES (:id_adherent, :id_salle, :id_activite, :id_equipement, :duree_minutes, :date_seance)'
        );
        $stmt->execute([
            'id_adherent'   => $seance->getIdAdherent(),
            'id_salle'      => $seance->getIdSalle(),
            'id_activite'   => $seance->getIdActivite(),
            'id_equipement' => $seance->getIdEquipement(),
            'duree_minutes' => $seance->getDureeMinutes(),
            'date_seance'   => $seance->getDateSeance(),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function countByAdherent(int $idAdherent): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM seances WHERE id_adherent = :id');
        $stmt->execute(['id' => $idAdherent]);
        return (int) $stmt->fetchColumn();
    }
}
