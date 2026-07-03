<?php

namespace App\Entities;

/**
 * Entite Adherent - encapsulation stricte (attributs prives + accesseurs).
 * Reflete fidelement la table `adherents` du MLD.
 */
class Adherent
{
    private ?int $idAdherent;
    private string $nom;
    private string $prenom;
    private string $email;
    private ?string $telephone;
    private string $dateInscription;
    private int $idSalle;

    public function __construct(
        string $nom,
        string $prenom,
        string $email,
        ?string $telephone,
        string $dateInscription,
        int $idSalle,
        ?int $idAdherent = null
    ) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->dateInscription = $dateInscription;
        $this->idSalle = $idSalle;
        $this->idAdherent = $idAdherent;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            $row['nom'],
            $row['prenom'],
            $row['email'],
            $row['telephone'] ?? null,
            $row['date_inscription'],
            (int) $row['id_salle'],
            isset($row['id_adherent']) ? (int) $row['id_adherent'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'id_adherent'      => $this->idAdherent,
            'nom'              => $this->nom,
            'prenom'           => $this->prenom,
            'email'            => $this->email,
            'telephone'        => $this->telephone,
            'date_inscription' => $this->dateInscription,
            'id_salle'         => $this->idSalle,
        ];
    }

    // Getters
    public function getIdAdherent(): ?int { return $this->idAdherent; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getEmail(): string { return $this->email; }
    public function getTelephone(): ?string { return $this->telephone; }
    public function getDateInscription(): string { return $this->dateInscription; }
    public function getIdSalle(): int { return $this->idSalle; }
    public function getNomComplet(): string { return $this->prenom . ' ' . $this->nom; }

    // Setters
    public function setIdAdherent(int $id): void { $this->idAdherent = $id; }
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setTelephone(?string $telephone): void { $this->telephone = $telephone; }
    public function setIdSalle(int $idSalle): void { $this->idSalle = $idSalle; }
}
