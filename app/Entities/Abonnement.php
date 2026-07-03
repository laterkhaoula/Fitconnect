<?php

namespace App\Entities;

/**
 * Entite Abonnement - types autorises : mensuel, trimestriel, annuel.
 * statut : actif | expire | resilie
 */
class Abonnement
{
    public const TYPES = ['mensuel', 'trimestriel', 'annuel'];
    public const STATUTS = ['actif', 'expire', 'resilie'];

    private ?int $idAbonnement;
    private int $idAdherent;
    private string $type;
    private string $dateDebut;
    private string $dateFin;
    private string $statut;

    public function __construct(
        int $idAdherent,
        string $type,
        string $dateDebut,
        string $dateFin,
        string $statut = 'actif',
        ?int $idAbonnement = null
    ) {
        if (!in_array($type, self::TYPES, true)) {
            throw new \InvalidArgumentException("Type d'abonnement invalide : {$type}");
        }
        if (!in_array($statut, self::STATUTS, true)) {
            throw new \InvalidArgumentException("Statut d'abonnement invalide : {$statut}");
        }
        if (strtotime($dateFin) <= strtotime($dateDebut)) {
            throw new \InvalidArgumentException('La date de fin doit etre posterieure a la date de debut.');
        }

        $this->idAdherent = $idAdherent;
        $this->type = $type;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->statut = $statut;
        $this->idAbonnement = $idAbonnement;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            (int) $row['id_adherent'],
            $row['type'],
            $row['date_debut'],
            $row['date_fin'],
            $row['statut'] ?? 'actif',
            isset($row['id_abonnement']) ? (int) $row['id_abonnement'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'id_abonnement' => $this->idAbonnement,
            'id_adherent'   => $this->idAdherent,
            'type'          => $this->type,
            'date_debut'    => $this->dateDebut,
            'date_fin'      => $this->dateFin,
            'statut'        => $this->statut,
        ];
    }

    /** Verifie si l'abonnement est valide a une date donnee (par defaut aujourd'hui). */
    public function estValideA(?string $date = null): bool
    {
        $date = $date ?? date('Y-m-d');
        return $this->statut === 'actif'
            && strtotime($date) >= strtotime($this->dateDebut)
            && strtotime($date) <= strtotime($this->dateFin);
    }

    // Getters
    public function getIdAbonnement(): ?int { return $this->idAbonnement; }
    public function getIdAdherent(): int { return $this->idAdherent; }
    public function getType(): string { return $this->type; }
    public function getDateDebut(): string { return $this->dateDebut; }
    public function getDateFin(): string { return $this->dateFin; }
    public function getStatut(): string { return $this->statut; }

    // Setters
    public function setIdAbonnement(int $id): void { $this->idAbonnement = $id; }
    public function setStatut(string $statut): void
    {
        if (!in_array($statut, self::STATUTS, true)) {
            throw new \InvalidArgumentException("Statut invalide : {$statut}");
        }
        $this->statut = $statut;
    }
}
