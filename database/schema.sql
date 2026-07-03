DROP DATABASE IF EXISTS fitconnect;
CREATE DATABASE fitconnect;
USE fitconnect;

-- Table salles
CREATE TABLE salles (
    id_salle INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    ville VARCHAR(100) NOT NULL
);

-- Table activites
CREATE TABLE activites (
    id_activite INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description VARCHAR(255)
);

-- Table equipements
CREATE TABLE equipements (
    id_equipement INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    id_salle INT NOT NULL,
    FOREIGN KEY (id_salle) REFERENCES salles(id_salle)
);

-- Table adherents
CREATE TABLE adherents (
    id_adherent INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telephone VARCHAR(30),
    date_inscription DATE NOT NULL,
    id_salle INT NOT NULL,
    FOREIGN KEY (id_salle) REFERENCES salles(id_salle)
);

-- Table abonnements
CREATE TABLE abonnements (
    id_abonnement INT AUTO_INCREMENT PRIMARY KEY,
    id_adherent INT NOT NULL,
    type ENUM('mensuel', 'trimestriel', 'annuel') NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    statut ENUM('actif', 'expire', 'resilie') DEFAULT 'actif',
    FOREIGN KEY (id_adherent) REFERENCES adherents(id_adherent)
);

-- Table seances
CREATE TABLE seances (
    id_seance INT AUTO_INCREMENT PRIMARY KEY,
    id_adherent INT NOT NULL,
    id_salle INT NOT NULL,
    id_activite INT NOT NULL,
    id_equipement INT,
    duree_minutes INT NOT NULL,
    date_seance DATETIME NOT NULL,

    FOREIGN KEY (id_adherent) REFERENCES adherents(id_adherent),
    FOREIGN KEY (id_salle) REFERENCES salles(id_salle),
    FOREIGN KEY (id_activite) REFERENCES activites(id_activite),
    FOREIGN KEY (id_equipement) REFERENCES equipements(id_equipement)
);
