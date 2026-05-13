CREATE TABLE departements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE types_conge (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle VARCHAR(100) NOT NULL,
    jours_annuels INTEGER NOT NULL DEFAULT 0,
    deductible INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE statuts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE employes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
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
    id INTEGER PRIMARY KEY AUTOINCREMENT,
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
    id INTEGER PRIMARY KEY AUTOINCREMENT,
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
(3, 'Andriam', 'Tiana', 'admin@techmada.mg', 'admin123', 'admin', 3, '2022-05-10', 1),
(4, 'Razafindra', 'Hery', 'hery@techmada.mg', 'emp123', 'employe', 1, '2024-03-12', 1),
(5, 'Raso', 'Mialy', 'mialy@techmada.mg', 'emp123', 'employe', 2, '2023-11-20', 1),
(6, 'Randriamampianina', 'Lova', 'lova@techmada.mg', 'emp123', 'employe', 3, '2022-08-05', 1);

INSERT IGNORE INTO soldes (id, employe_id, type_conge_id, annee, jours_attribues, jours_pris) VALUES
(1, 1, 1, 2025, 30, 12),
(2, 1, 2, 2025, 10, 2),
(3, 1, 3, 2025, 5, 4),
(4, 4, 1, 2025, 30, 8),
(5, 4, 2, 2025, 10, 1),
(6, 5, 1, 2025, 30, 14),
(7, 5, 2, 2025, 10, 0),
(8, 6, 1, 2025, 30, 6),
(9, 6, 3, 2025, 5, 1);

INSERT IGNORE INTO conges (id, employe_id, type_conge_id, date_debut, date_fin, nb_jours, motif, statut_id, commentaire_rh, traite_par, created_at) VALUES
(1, 1, 1, '2025-06-16', '2025-06-20', 5, 'Congé annuel', 1, NULL, NULL, '2025-06-01 08:00:00'),
(2, 1, 2, '2025-06-02', '2025-06-03', 2, 'Repos médical', 2, 'Demande validée', 2, '2025-06-02 09:15:00'),
(3, 1, 1, '2025-05-12', '2025-05-16', 5, 'Vacances', 2, 'Approuvé', 2, '2025-05-12 10:30:00'),
(4, 1, 3, '2025-07-10', '2025-07-10', 1, 'Autorisation spéciale', 3, 'Motif incomplet', 2, '2025-07-01 11:00:00'),
(5, 4, 1, '2025-08-01', '2025-08-05', 5, 'Premier congé', 1, NULL, NULL, '2025-07-28 14:00:00'),
(6, 4, 1, '2025-09-15', '2025-09-19', 5, 'Voyage familial', 1, NULL, NULL, '2025-09-01 08:20:00'),
(7, 5, 2, '2025-07-08', '2025-07-09', 2, 'Repos médical', 2, 'Validé par RH', 2, '2025-07-08 09:10:00'),
(8, 6, 1, '2025-10-20', '2025-10-24', 5, 'Congé annuel', 3, 'Refus pour période chargée', 2, '2025-10-01 10:00:00'),
(9, 5, 3, '2025-11-03', '2025-11-03', 1, 'Autorisation spéciale', 1, NULL, NULL, '2025-10-28 11:00:00');