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

INSERT INTO statuts(libelle) VALUES
('en_attente'),
('approuve'),
('refuse'),
('annule');