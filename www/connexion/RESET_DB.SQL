ALTER TABLE relance_client
ADD COLUMN commentaire TEXT;

ALTER TABLE relance_client
MODIFY COLUMN id_contact_client INT NULL;









USE OpenRelance;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS clients, contactes_clients, relance_client, factures, relance_facture, commentaires, commentaires_factures;
SET FOREIGN_KEY_CHECKS = 1;

-- Créer la nouvelle structure de la base de données

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
    mail_contactes_clients VARCHAR(255),
    telphone_contactes_clients VARCHAR(20),
    commentaire_contactes_clients TEXT,
    id_clients INT,
    id_user_open_relance INT,
    FOREIGN KEY (id_clients) REFERENCES clients(id),
    FOREIGN KEY (id_user_open_relance) REFERENCES user_open_relance(id)
);

CREATE TABLE relance_client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_relance VARCHAR(255) NOT NULL,
    date_relance DATE NOT NULL,
    id_contact_client INT NULL,
    id_user_open_relance INT,
    commentaire TEXT,
    FOREIGN KEY (id_contact_client) REFERENCES contactes_clients(id),
    FOREIGN KEY (id_user_open_relance) REFERENCES user_open_relance(id)
);


CREATE TABLE factures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numeros_de_facture VARCHAR(255) NOT NULL UNIQUE,
    date_emission_facture DATE NOT NULL,
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
