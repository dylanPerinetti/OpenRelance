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
    email_user_open_relance VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
);

-- Création de la table clients
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_client VARCHAR(255) NOT NULL,
    numeros_parma VARCHAR(255) NOT NULL,
    id_user_open_relance INT,
    FOREIGN KEY (id_user_open_relance) REFERENCES user_open_relance(id)
);

-- Création de la table contactes_clients
CREATE TABLE contactes_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fonction_contactes_clients VARCHAR(255) NOT NULL,
    nom_contactes_clients VARCHAR(255) NOT NULL,
    mail_contactes_clients VARCHAR(255) NOT NULL UNIQUE,
    telphone_contactes_clients VARCHAR(20) NOT NULL,
    id_clients INT,
    FOREIGN KEY (id_clients) REFERENCES clients(id)
);

-- Création de la table relance_client
CREATE TABLE relance_client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_relance VARCHAR(255) NOT NULL, -- le type de relance (par ex: mail, appel, courrier 1, courrier 2, recommandé ...)
    date_relance DATE NOT NULL, -- date de la relance
    id_contact_client INT,
    id_user_open_relance INT,
    FOREIGN KEY (id_contact_client) REFERENCES contactes_clients(id),
    FOREIGN KEY (id_user_open_relance) REFERENCES user_open_relance(id)
);

-- Création de la table factures
CREATE TABLE factures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numeros_de_facture VARCHAR(255) NOT NULL UNIQUE,
    date_echeance_payment DATE NOT NULL,
    montant_facture DECIMAL(10, 2) NOT NULL,
    montant_reste_a_payer DECIMAL(10, 2) NOT NULL,
    id_clients INT,
    id_relance_client INT,
    id_user_open_relance INT,
    FOREIGN KEY (id_clients) REFERENCES clients(id),
    FOREIGN KEY (id_relance_client) REFERENCES relance_client(id),
    FOREIGN KEY (id_user_open_relance) REFERENCES user_open_relance(id)
);

-- Création de la table commentaires
CREATE TABLE commentaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_commentaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    message_commentaire TEXT NOT NULL,
    id_factures INT,
    id_user_open_relance INT,
    FOREIGN KEY (id_factures) REFERENCES factures(id),
    FOREIGN KEY (id_user_open_relance) REFERENCES user_open_relance(id)
);
