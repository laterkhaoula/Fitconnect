-- ============================================================
-- INSERT INTO salles
-- ============================================================

INSERT INTO salles (nom, adresse, ville) VALUES
('FitConnect Centre', '10 Avenue Mohammed V', 'Casablanca'),
('FitConnect Agdal', '25 Rue Hassan II', 'Rabat'),
('FitConnect Gueliz', '15 Boulevard Zerktouni', 'Marrakech'),
('FitConnect Malabata', '8 Avenue Mohammed VI', 'Tanger');

-- ============================================================
-- INSERT INTO activites
-- ============================================================

INSERT INTO activites (nom, description) VALUES
('Musculation', 'Entrainement avec machines et poids'),
('Cardio', 'Exercices pour ameliorer l endurance'),
('Yoga', 'Seances de relaxation et flexibilite'),
('CrossFit', 'Entrainement intensif fonctionnel'),
('Cycling', 'Cours de velo en salle');

-- ============================================================
-- INSERT INTO equipements
-- ============================================================

INSERT INTO equipements (nom, id_salle) VALUES
('Tapis de course', 1),
('Velo elliptique', 1),
('Banc de musculation', 2),
('Rameur', 2),
('Halteres', 3),
('Barre olympique', 3),
('Velo spinning', 4),
('Machine a squat', 4);

-- ============================================================
-- INSERT INTO adherents
-- ============================================================

INSERT INTO adherents
(nom, prenom, email, telephone, date_inscription, id_salle)
VALUES
('Alaoui', 'Ahmed', 'ahmed@fitconnect.com', '0611111111', '2026-01-10', 1),
('Benali', 'Sara', 'sara@fitconnect.com', '0622222222', '2026-01-15', 2),
('Amrani', 'Youssef', 'youssef@fitconnect.com', '0633333333', '2026-02-01', 3),
('Idrissi', 'Khadija', 'khadija@fitconnect.com', '0644444444', '2026-02-10', 4),
('Tazi', 'Omar', 'omar@fitconnect.com', '0655555555', '2026-03-01', 1);

-- ============================================================
-- INSERT INTO abonnements
-- ============================================================

INSERT INTO abonnements
(id_adherent, type, date_debut, date_fin, statut)
VALUES
(1, 'annuel', '2026-01-10', '2027-01-10', 'actif'),
(2, 'mensuel', '2026-01-15', '2026-02-15', 'expire'),
(3, 'trimestriel', '2026-02-01', '2026-05-01', 'actif'),
(4, 'annuel', '2026-02-10', '2027-02-10', 'actif'),
(5, 'mensuel', '2026-03-01', '2026-04-01', 'resilie');

-- ============================================================
-- INSERT INTO seances
-- ============================================================

INSERT INTO seances
(id_adherent, id_salle, id_activite, id_equipement,
duree_minutes, date_seance)
VALUES
(1, 1, 1, 1, 60, '2026-06-01 10:00:00'),
(2, 2, 3, NULL, 45, '2026-06-02 15:00:00'),
(3, 3, 4, 5, 90, '2026-06-03 18:00:00'),
(4, 4, 5, 7, 50, '2026-06-04 11:00:00'),
(5, 1, 2, 2, 40, '2026-06-05 09:00:00');
