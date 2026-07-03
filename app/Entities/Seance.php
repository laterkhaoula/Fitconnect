<?php

namespace App\Entities;

class Seance
{
    private ?int $idSeance;
    private int $idAdherent;
    private int $idSalle;
    private int $idActivite;
    private ?int $idEquipement;
    private int $dureeMinutes;
    private string $dateSeance;

    public function __construct(
        int $idAdherent,
        int $idSalle,
        int $idActivite,
        ?int $idEquipement,
        int $dureeMinutes,
        string $dateSeance,
        ?int $idSeance = null
    ) {
        if ($dureeMinutes <= 0) {
            throw new \InvalidArgumentException('La duree doit etre positive.');
        }

        $this->idAdherent = $idAdherent;
        $this->idSalle = $idSalle;
        $this->idActivite = $idActivite;
        $this->idEquipement = $idEquipement;
        $this->dureeMinutes = $dureeMinutes;
        $this->dateSeance = $dateSeance;
        $this->idSeance = $idSeance;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            (int) $row['id_adherent'],
            (int) $row['id_salle'],
            (int) $row['id_activite'],
            isset($row['id_equipement']) ? (int) $row['id_equipement'] : null,
            (int) $row['duree_minutes'],
            $row['date_seance'],
            isset($row['id_seance']) ? (int) $row['id_seance'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'id_seance'     => $this->idSeance,
            'id_adherent'   => $this->idAdherent,
            'id_salle'      => $this->idSalle,
            'id_activite'   => $this->idActivite,
            'id_equipement' => $this->idEquipement,
            'duree_minutes' => $this->dureeMinutes,
            'date_seance'   => $this->dateSeance,
        ];
    }

    // Getters
    public function getIdSeance(): ?int { return $this->idSeance; }
    public function getIdAdherent(): int { return $this->idAdherent; }
    public function getIdSalle(): int { return $this->idSalle; }
    public function getIdActivite(): int { return $this->idActivite; }
    public function getIdEquipement(): ?int { return $this->idEquipement; }
    public function getDureeMinutes(): int { return $this->dureeMinutes; }
    public function getDateSeance(): string { return $this->dateSeance; }

    public function setIdSeance(int $id): void { $this->idSeance = $id; }
}
