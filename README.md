# FitConnect — Backend PHP OOP (MySQL + PDO)

Backend de gestion d'un reseau de 4 salles de sport (adherents, abonnements,
activites, equipements, seances), realise pour Karim Benslimane (fondateur
de FitConnect) dans le cadre du projet DevAcademy.

## 1. Contexte

Chaque salle enregistrait manuellement ses seances dans des fichiers Excel
independants. Cette application centralise ces donnees dans une base
relationnelle unique et propose une architecture en couches (Entities,
Repositories, Services, Controllers) afin d'etre facilement reprise par
l'equipe technique de FitConnect.

## 2. Arborescence du projet

```
fitconnect/
├── config/
│   └── Database.php          # Connexion PDO centralisee (singleton)
├── app/
│   ├── Entities/              # Adherent, Abonnement, Seance (encapsulation stricte)
│   ├── Repositories/          # Acces aux donnees, requetes PDO parametrees
│   ├── Services/               # Logique metier (regles de gestion)
│   └── Controllers/            # Orchestration Services <-> Repositories <-> Vues
├── views/
│   ├── adherents/               index.php, create.php
│   ├── abonnements/             index.php, create.php
│   ├── dashboard/                index.php
│   └── partials/                 nav.php
├── database/
│   ├── schema.sql               # CREATE TABLE issues du MLD
│   └── seed.sql                 # Jeu de donnees de test
├── public/
│   ├── index.php                # Point d'entree unique (routeur)
│   ├── test.php                 # Tests manuels des couches
│   └── assets/style.css
├── vendor_autoload.php         # Autoloader PSR-4-like (sans dependance Composer)
├── README.md
└── .gitignore
```

## 3. Modelisation (MCD → MLD)

### 3.1 MCD (entites et associations)

- **Salle** (id_salle, nom, adresse, ville)
- **Adherent** (id_adherent, nom, prenom, email, telephone, date_inscription)
- **Abonnement** (id_abonnement, type, date_debut, date_fin, statut)
- **Activite** (id_activite, nom, description)
- **Equipement** (id_equipement, nom)
- **Seance** (id_seance, duree_minutes, date_seance)

Associations :
- Salle (1,1) — S'INSCRIT — Adherent (0,n) : chaque adherent est inscrit dans une seule salle.
- Adherent (1,1) — SOUSCRIT — Abonnement (0,n) : un adherent peut avoir un historique
  d'abonnements, mais un seul **actif** a la fois (regle geree en Service).
- Adherent (1,1) — EFFECTUE — Seance (0,n)
- Salle (1,1) — ACCUEILLE — Seance (0,n)
- Activite (1,1) — CONCERNE — Seance (0,n)
- Equipement (0,1) — UTILISE — Seance (0,n) : l'equipement est optionnel.
- Salle (1,1) — POSSEDE — Equipement (0,n)

### 3.2 MLD (derive du MCD)

```
salles(id_salle, nom, adresse, ville)
activites(id_activite, nom, description)
equipements(id_equipement, nom, #id_salle)
adherents(id_adherent, nom, prenom, email, telephone, date_inscription, #id_salle)
abonnements(id_abonnement, type, date_debut, date_fin, statut, #id_adherent)
seances(id_seance, duree_minutes, date_seance, #id_adherent, #id_salle, #id_activite, #id_equipement)
```

Cle etrangere = `#`. Normalisation verifiee : chaque table est en 3NF
(pas de dependance transitive, chaque attribut non-cle depend uniquement
de la cle primaire).

Le script `database/schema.sql` traduit ce MLD en `CREATE TABLE` avec
contraintes `PRIMARY KEY`, `FOREIGN KEY`, `NOT NULL`, `UNIQUE` et `CHECK`.

## 4. Regles de gestion et leur implementation

| Regle | Ou est-elle appliquee |
|---|---|
| Un adherent a une seule salle d'inscription | Contrainte FK `id_salle` NOT NULL sur `adherents` |
| Un seul abonnement actif a la fois | `AbonnementService::souscrire()` resilie l'ancien avant de creer le nouveau |
| Seance enregistree seulement si abonnement valide | `SeanceService::enregistrer()` verifie `AbonnementService::estAbonneValide()` |
| Adherent non supprimable s'il a des seances/abonnement en cours | `AdherentService::supprimer()` + contraintes `ON DELETE RESTRICT` en base |
| Requetes exclusivement parametrees | Toutes les methodes des Repositories utilisent `PDO::prepare()` |

## 5. Installation

### Prerequis
- PHP >= 8.0 (typed properties, constructor promotion non utilisee ici mais compatible 8.0+)
- MySQL / MariaDB
- Un serveur local (XAMPP, WAMP, MAMP) ou `php -S`

### Etapes

1. Creer la base de donnees :
   ```bash
   mysql -u root -p < database/schema.sql
   mysql -u root -p < database/seed.sql
   ```

2. Configurer la connexion si besoin (par defaut : `localhost`, `root`,
   mot de passe vide) dans `config/Database.php`, ou via variables
   d'environnement `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.

3. Lancer le serveur de developpement PHP depuis la racine du projet :
   ```bash
   php -S localhost:8000 -t public
   ```

4. Ouvrir `http://localhost:8000` dans le navigateur.

5. Verifier les couches independamment :
   ```bash
   php public/test.php
   ```

## 6. Points d'entree de l'application

- `index.php?page=dashboard` — Tableau de bord + enregistrement de seance
- `index.php?page=adherents` — Liste des adherents
- `index.php?page=adherents&action=create` — Formulaire de creation
- `index.php?page=abonnements` — Liste des abonnements
- `index.php?page=abonnements&action=create` — Formulaire de souscription

## 7. Suite du projet (hors code)

- **Jira** : backlog a creer avec les epics "Conception", "Base de donnees",
  "Backend PHP", et les User Stories US1 a US12 fournies par DevAcademy,
  reparties sur les sprints de la duree du projet.
- **GitHub** : `git init`, premier commit avec cette arborescence, puis
  push vers un depot distant a lier dans les livrables.

## 8. Limites connues / ameliorations possibles

- Pas de gestion de sessions/authentification (hors perimetre de la demande initiale).
- Le routeur `public/index.php` est volontairement minimaliste (pas de
  framework) pour rester lisible ; une montee en charge justifierait un
  routeur dedie.
- La transition automatique `actif -> expire` en fonction de la date du
  jour n'est pas schedulee (pas de tache cron) ; `estValideA()` compense
  ce point en verifiant la date a chaque controle.
