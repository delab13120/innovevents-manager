CREATE DATABASE IF NOT EXISTS innovevents_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE innovevents_db;

-- 1. Table des utilisateurs (Admin Chloé, Employés, Clients)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL,
    role ENUM('client', 'employe', 'admin') DEFAULT 'client',
    must_change_password BOOLEAN DEFAULT FALSE,
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Table des prospects (Demandes de devis visiteurs)
CREATE TABLE prospects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entreprise VARCHAR(150) NOT NULL,
    nom_prenom VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    lieu VARCHAR(255) NOT NULL,
    type_evenement VARCHAR(100) NOT NULL,
    date_souhaitee DATE NOT NULL,
    nb_participants INT NOT NULL,
    besoin TEXT NOT NULL,
    statut ENUM('a_contacter', 'converti', 'echoue') DEFAULT 'a_contacter',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. Table des événements
CREATE TABLE evenements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    titre VARCHAR(200) NOT NULL,
    type_evenement VARCHAR(100) NOT NULL,
    theme VARCHAR(100) NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    lieu VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) DEFAULT 'default.jpg',
    statut ENUM('brouillon', 'accepte', 'en_cours', 'termine', 'annule') DEFAULT 'brouillon',
    visible_public BOOLEAN DEFAULT FALSE,
    accord_client BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 4. Table des devis et prestations
CREATE TABLE devis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evenement_id INT NOT NULL,
    client_id INT NOT NULL,
    montant_ht DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tva DECIMAL(5,2) NOT NULL DEFAULT 20.00,
    montant_ttc DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    statut ENUM('etude_client', 'accepte', 'modification', 'refuse') DEFAULT 'etude_client',
    motif_modification TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Table des lignes de prestations d'un devis
CREATE TABLE devis_lignes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    devis_id INT NOT NULL,
    libelle VARCHAR(255) NOT NULL,
    montant_ht DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE CASCADE
);

-- 6. Table des notes collaboratives
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evenement_id INT NULL,
    auteur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE,
    FOREIGN KEY (auteur_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 7. Table des tâches
CREATE TABLE taches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evenement_id INT NOT NULL,
    employe_id INT NOT NULL,
    titre VARCHAR(200) NOT NULL,
    statut ENUM('a_faire', 'en_cours', 'termine') DEFAULT 'a_faire',
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE,
    FOREIGN KEY (employe_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 8. Table des avis clients
CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evenement_id INT NOT NULL,
    client_id INT NOT NULL,
    note INT CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT NOT NULL,
    valide BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
);

-- JEU DE DONNÉES INITIAL
INSERT INTO users (email, password, nom, prenom, username, role) VALUES
('chloe@innovevents.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Martin', 'Chloé', 'chloe_admin', 'admin'),
('jose@innovevents.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dupont', 'José', 'jose_emp', 'employe'),
('client@entreprise.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Durand', 'Sophie', 'sophie_cli', 'client');

INSERT INTO evenements (client_id, titre, type_evenement, theme, date_debut, date_fin, lieu, statut, visible_public, accord_client) VALUES
(3, 'Séminaire Innovation 2026', 'Séminaire', 'Tech & IA', '2026-06-15 09:00:00', '2026-06-16 18:00:00', 'Palais des Congrès, Paris', 'accepte', TRUE, TRUE),
(3, 'Gala Annuel Luxe', 'Soirée d\'entreprise', 'Gastronomie', '2026-09-20 19:00:00', '2026-09-21 02:00:00', 'Château de Versailles', 'brouillon', FALSE, FALSE);

INSERT INTO avis (evenement_id, client_id, note, commentaire, valide) VALUES
(1, 3, 5, 'Organisation irréprochable par toute l\'équipe d\'Innov\'Events !', TRUE);
