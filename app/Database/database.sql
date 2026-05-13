DROP DATABASE IF EXISTS rh_interne;
CREATE DATABASE IF NOT EXISTS rh_interne;
USE rh_interne;

CREATE TABLE departements (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE types_conge (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(100) NOT NULL,
    jours_annuels INTEGER NOT NULL DEFAULT 0,
    deductible INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE statuts (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE employes (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    departement_id INTEGER,
    date_embauche DATE NOT NULL,
    actif INTEGER NOT NULL DEFAULT 1,

    FOREIGN KEY (departement_id)
        REFERENCES departements(id)
);

CREATE TABLE soldes (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    employe_id INTEGER NOT NULL,
    type_conge_id INTEGER NOT NULL,
    annee INTEGER NOT NULL,
    jours_attribues INTEGER NOT NULL DEFAULT 0,
    jours_pris INTEGER NOT NULL DEFAULT 0,

    FOREIGN KEY (employe_id)
        REFERENCES employes(id),

    FOREIGN KEY (type_conge_id)
        REFERENCES types_conge(id)
);

CREATE TABLE conges (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    employe_id INTEGER NOT NULL,
    type_conge_id INTEGER NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    nb_jours INTEGER NOT NULL,
    motif TEXT,
    statut_id INTEGER NOT NULL,
    commentaire_rh TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    traite_par INTEGER,


    FOREIGN KEY (employe_id)
        REFERENCES employes(id),

    FOREIGN KEY (type_conge_id)
        REFERENCES types_conge(id),

    FOREIGN KEY (statut_id)
        REFERENCES statuts(id),

    FOREIGN KEY (traite_par)
        REFERENCES employes(id)
);

INSERT IGNORE INTO statuts(libelle) VALUES
('en_attente'),
('approuve'),
('refuse'),
('annule');

INSERT IGNORE INTO departements (id, nom, description) VALUES
(1, 'Informatique', 'Equipe infrastructure et applications'),
(2, 'Ressources Humaines', 'Gestion du personnel et des congés'),
(3, 'Comptabilite', 'Suivi financier et paie');

INSERT IGNORE INTO types_conge (id, libelle, jours_annuels, deductible) VALUES
(1, 'Congé annuel', 30, 1),
(2, 'Congé maladie', 10, 1),
(3, 'Congé spécial', 5, 1);

INSERT IGNORE INTO employes (id, nom, prenom, email, password, role, departement_id, date_embauche, actif) VALUES
(1, 'Rakoto', 'Soa', 'employe@techmada.mg', 'emp123', 'employe', 1, '2024-01-15', 1),
(2, 'Ranaivo', 'Mira', 'rh@techmada.mg', 'rh123', 'rh', 2, '2023-09-01', 1),
(3, 'Andriam', 'Tiana', 'admin@techmada.mg', 'admin123', 'admin', 3, '2022-05-10', 1);

INSERT IGNORE INTO soldes (id, employe_id, type_conge_id, annee, jours_attribues, jours_pris) VALUES
(1, 1, 1, 2025, 30, 12),
(2, 1, 2, 2025, 10, 2),
(3, 1, 3, 2025, 5, 4),
(4, 4, 1, 2025, 0, 0);

INSERT IGNORE INTO conges (id, employe_id, type_conge_id, date_debut, date_fin, nb_jours, motif, statut_id, commentaire_rh, traite_par, created_at) VALUES
(1, 1, 1, '2025-06-16', '2025-06-20', 5, 'Congé annuel', 1, NULL, NULL, '2025-06-01 08:00:00'),
(2, 1, 2, '2025-06-02', '2025-06-03', 2, 'Repos médical', 2, 'Demande validée', 2, '2025-06-02 09:15:00'),
(3, 1, 1, '2025-05-12', '2025-05-16', 5, 'Vacances', 2, 'Approuvé', 2, '2025-05-12 10:30:00'),
(4, 1, 3, '2025-07-10', '2025-07-10', 1, 'Autorisation spéciale', 3, 'Motif incomplet', 2, '2025-07-01 11:00:00'),
(5, 4, 1, '2025-08-01', '2025-08-05', 5, 'Premier congé', 1, NULL, NULL, '2025-07-28 14:00:00');