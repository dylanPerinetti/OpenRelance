-- Création de la base de données
CREATE DATABASE OpenRelance;

-- Utilisation de la base de données
USE OpenRelance;

-- Création des tables
CREATE TABLE user_open_relance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_user_open_relance VARCHAR(255) NOT NULL,
    prenom_user_open_relance VARCHAR(255) NOT NULL,
    initial_user_open_relance CHAR(2) NOT NULL,
    type_de_profil INT NOT NULL,
    email_user_open_relance VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
);

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_client VARCHAR(255) NOT NULL,
    numeros_parma VARCHAR(255) NOT NULL,
    id_user_open_relance INT,
    FOREIGN KEY (id_user_open_relance) REFERENCES user_open_relance(id)
);

CREATE TABLE contactes_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fonction_contactes_clients VARCHAR(255) NOT NULL,
    nom_contactes_clients VARCHAR(255),
    mail_contactes_clients VARCHAR(255) NOT NULL,
    telphone_contactes_clients VARCHAR(20) NOT NULL,
    id_clients INT,
    FOREIGN KEY (id_clients) REFERENCES clients(id)
);

CREATE TABLE relance_client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_relance VARCHAR(255) NOT NULL,
    date_relance DATE NOT NULL,
    id_contact_client INT,
    id_user_open_relance INT,
    FOREIGN KEY (id_contact_client) REFERENCES contactes_clients(id),
    FOREIGN KEY (id_user_open_relance) REFERENCES user_open_relance(id)
);

CREATE TABLE factures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numeros_de_facture VARCHAR(255) NOT NULL UNIQUE,
    date_echeance_payment DATE NOT NULL,
    montant_facture DECIMAL(10, 2) NOT NULL,
    montant_reste_a_payer DECIMAL(10, 2) NOT NULL,
    id_clients INT,
    id_user_open_relance INT,
    FOREIGN KEY (id_clients) REFERENCES clients(id),
    FOREIGN KEY (id_user_open_relance) REFERENCES user_open_relance(id)
);

CREATE TABLE relance_facture (
    id_relance_client INT,
    id_facture INT,
    PRIMARY KEY (id_relance_client, id_facture),
    FOREIGN KEY (id_relance_client) REFERENCES relance_client(id),
    FOREIGN KEY (id_facture) REFERENCES factures(id)
);

CREATE TABLE commentaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_commentaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    message_commentaire TEXT NOT NULL,
    id_user_open_relance INT,
    FOREIGN KEY (id_user_open_relance) REFERENCES user_open_relance(id)
);

CREATE TABLE commentaires_factures (
    id_commentaire INT,
    id_facture INT,
    PRIMARY KEY (id_commentaire, id_facture),
    FOREIGN KEY (id_commentaire) REFERENCES commentaires(id),
    FOREIGN KEY (id_facture) REFERENCES factures(id)
);
