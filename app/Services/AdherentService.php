<?php

namespace App\Services;

use App\Entities\Adherent;
use App\Repositories\AdherentRepository;
use InvalidArgumentException;
use RuntimeException;

class AdherentService
{
    private AdherentRepository $adherentRepository;

    public function __construct(AdherentRepository $adherentRepository)
    {
        $this->adherentRepository = $adherentRepository;
    }

    public function listerTous(): array
    {
        return $this->adherentRepository->findAll();
    }

    public function trouverParId(int $id): ?Adherent
    {
        return $this->adherentRepository->findById($id);
    }

    public function inscrire(string $nom, string $prenom, string $email, ?string $telephone, int $idSalle): int
    {
        if (trim($nom) === '' || trim($prenom) === '') {
            throw new InvalidArgumentException('Le nom et le prenom sont obligatoires.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Adresse email invalide.');
        }
        if ($this->adherentRepository->findByEmail($email) !== null) {
            throw new InvalidArgumentException('Cet email est deja utilise par un autre adherent.');
        }

        $adherent = new Adherent($nom, $prenom, $email, $telephone, date('Y-m-d'), $idSalle);
        return $this->adherentRepository->create($adherent);
    }

    public function modifier(Adherent $adherent): bool
    {
        return $this->adherentRepository->update($adherent);
    }

    /**
     * Regle de gestion : un adherent ne peut pas etre supprime s'il possede
     * des seances enregistrees ou un abonnement en cours.
     */
    public function supprimer(int $idAdherent): bool
    {
        if ($this->adherentRepository->hasSeances($idAdherent)) {
            throw new RuntimeException("Impossible de supprimer : l'adherent possede des seances enregistrees.");
        }
        if ($this->adherentRepository->hasAbonnementEnCours($idAdherent)) {
            throw new RuntimeException("Impossible de supprimer : l'adherent possede un abonnement en cours.");
        }
        return $this->adherentRepository->delete($idAdherent);
    }
}
