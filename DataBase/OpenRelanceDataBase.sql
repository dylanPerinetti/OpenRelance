-- Création de la base de données
CREATE DATABASE OpenRelance;

-- Utilisation de la base de données
USE OpenRelance;

-- Création de la table user_open_relance
CREATE TABLE user_open_relance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_user_open_relance VARCHAR(255) NOT NULL,
    prenom_user_open_relance VARCHAR(255) NOT NULL,
    initial_user_open_relance CHAR(2) NOT NULL,
    type_de_profil INT NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL -- Ajout du champ mot_de_passe
);

-- Création de la table clients
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_client VARCHAR(255) NOT NULL,
    numeros_parma VARCHAR(255) NOT NULL
);

-- Création de la table factures
CREATE TABLE factures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numeros_de_facture VARCHAR(255) NOT NULL,
    date_echeance_payment DATE NOT NULL,
    montant_facture DECIMAL(10, 2) NOT NULL,
    id_entreprise INT,
    FOREIGN KEY (id_entreprise) REFERENCES clients(id)
);

-- Création de la table commentaires
CREATE TABLE commentaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_commentaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    message_commentaire TEXT NOT NULL,
    id_factures INT,
    FOREIGN KEY (id_factures) REFERENCES factures(id)
);

-- Création de la table contactes_clients
CREATE TABLE contactes_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fonction_contactes_clients VARCHAR(255) NOT NULL,
    nom_contactes_clients VARCHAR(255) NOT NULL,
    mail_contactes_clients VARCHAR(255) NOT NULL,
    telphone_contactes_clients VARCHAR(20) NOT NULL,
    id_entreprise INT,
    FOREIGN KEY (id_entreprise) REFERENCES clients(id)
);
