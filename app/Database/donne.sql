INSERT INTO departements (nom, description) VALUES
('Informatique', 'Service développement et maintenance des systèmes'),
('Ressources Humaines', 'Gestion du personnel et recrutement'),
('Finance', 'Gestion comptable et financière'),
('Logistique', 'Gestion des stocks et approvisionnements');


INSERT INTO types_conge (libelle, jours_annuels, deductible) VALUES
('Congé annuel', 30, 1),
('Congé maladie', 15, 0),
('Congé maternité', 90, 0),
('Congé sans solde', 0, 1);

INSERT INTO employes (nom, prenom, email, password, role, departement_id, date_embauche, actif) VALUES
('Rakoto', 'Jean', 'jean.rakoto@entreprise.com', 'pass123', 'admin', 1, '2022-01-10', 1),
('Rasoanaivo', 'Marie', 'marie.rasoanaivo@entreprise.com', 'pass123', 'rh', 2, '2021-05-12', 1),
('Andrianina', 'Paul', 'paul.andrianina@entreprise.com', 'pass123', 'employe', 1, '2023-03-01', 1),
('Razafindrabe', 'Luc', 'luc.razafindrabe@entreprise.com', 'pass123', 'employe', 3, '2020-11-20', 1),
('Rajaonarison', 'Sophie', 'sophie.rajaonarison@entreprise.com', 'pass123', 'employe', 4, '2024-02-15', 1);

INSERT INTO soldes (employe_id, type_conge_id, annee, jours_attribues, jours_pris) VALUES
(1, 1, 2025, 30, 5),
(1, 2, 2025, 15, 2),

(2, 1, 2025, 30, 10),
(2, 3, 2025, 90, 0),

(3, 1, 2025, 30, 0),
(3, 4, 2025, 0, 0),

(4, 1, 2025, 30, 12),
(4, 2, 2025, 15, 3),

(5, 1, 2025, 30, 1),
(5, 3, 2025, 90, 0);

INSERT INTO conges (
    employe_id,
    type_conge_id,
    date_debut,
    date_fin,
    nb_jours,
    motif,
    statut_id,
    commentaire_rh,
    traite_par
) VALUES
(3, 1, '2025-06-10', '2025-06-15', 5, 'Vacances famille', 1, NULL, NULL),

(2, 3, '2025-07-01', '2025-09-30', 90, 'Congé maternité', 2, 'Validé sans problème', 1),

(4, 2, '2025-05-10', '2025-05-20', 10, 'Maladie', 2, 'Dossier médical fourni', 2),

(5, 1, '2025-08-01', '2025-08-05', 4, 'Voyage', 3, 'Refus pour manque de personnel', 2),

(1, 4, '2025-09-01', '2025-09-10', 10, 'Congé personnel', 1, NULL, NULL);

