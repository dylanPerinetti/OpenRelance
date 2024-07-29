# OpenRelance Database

## Description

La base de données **OpenRelance** est conçue pour gérer les informations liées aux utilisateurs, clients, factures, commentaires et contacts clients de la plateforme de relance de paiements [OpenRelance](https://github.com/dylanPerinetti/OpenRelance).

## Structure de la base de données

![Diagramme de la base de données](https://github.com/dylanPerinetti/OpenRelance/raw/main/DataBase/ImageMCD.png)

### Tables

1. **user_open_relance**
   - `id` : Identifiant unique de l'utilisateur (auto-incrémenté).
   - `nom_user_open_relance` : Nom de l'utilisateur.
   - `prenom_user_open_relance` : Prénom de l'utilisateur.
   - `initial_user_open_relance` : Initiales de l'utilisateur (2 caractères).
   - `type_de_profil` : Type de profil de l'utilisateur (représenté par un entier).
   - `email_user_open_relance` : Adresse email de l'utilisateur.
   - `mot_de_passe` : Mot de passe de l'utilisateur.

2. **clients**
   - `id` : Identifiant unique du client (auto-incrémenté).
   - `nom_client` : Nom du client.
   - `numeros_parma` : Numéro unique du client.
   - `id_user_open_relance` : Identifiant de l'utilisateur associé (clé étrangère vers `user_open_relance`).

3. **contactes_clients**
   - `id` : Identifiant unique du contact client (auto-incrémenté).
   - `fonction_contactes_clients` : Fonction du contact client.
   - `nom_contactes_clients` : Nom du contact client (peut être NULL).
   - `mail_contactes_clients` : Adresse email du contact client.
   - `telphone_contactes_clients` : Numéro de téléphone du contact client.
   - `commentaire_contactes_clients` : Commentaire sur le contact client.
   - `id_clients` : Identifiant du client associé (clé étrangère vers `clients`).

4. **relance_client**
   - `id` : Identifiant unique de la relance client (auto-incrémenté).
   - `type_relance` : Type de relance (par ex: mail, appel, courrier 1, courrier 2, recommandé, etc.).
   - `date_relance` : Date de la relance.
   - `id_contact_client` : Identifiant du contact client (clé étrangère vers `contactes_clients`).
   - `id_user_open_relance` : Identifiant de l'utilisateur ayant initié la relance (clé étrangère vers `user_open_relance`).

5. **factures**
   - `id` : Identifiant unique de la facture (auto-incrémenté).
   - `numeros_de_facture` : Numéro de la facture (contenant des lettres et des chiffres).
   - `date_echeance_payment` : Date d'échéance du paiement.
   - `montant_facture` : Montant de la facture.
   - `montant_reste_a_payer` : Montant restant à payer sur la facture.
   - `id_clients` : Identifiant du client associé (clé étrangère vers `clients`).
   - `id_user_open_relance` : Identifiant de l'utilisateur associé (clé étrangère vers `user_open_relance`).

6. **relance_facture**
   - `id_relance_client` : Identifiant de la relance client (clé étrangère vers `relance_client`).
   - `id_facture` : Identifiant de la facture (clé étrangère vers `factures`).
   - **Clé primaire composée** : (`id_relance_client`, `id_facture`).

7. **commentaires**
   - `id` : Identifiant unique du commentaire (auto-incrémenté).
   - `date_commentaire` : Date du commentaire (générée automatiquement).
   - `message_commentaire` : Contenu du commentaire.
   - `id_user_open_relance` : Identifiant de l'utilisateur ayant fait le commentaire (clé étrangère vers `user_open_relance`).

8. **commentaires_factures**
   - `id_commentaire` : Identifiant du commentaire (clé étrangère vers `commentaires`).
   - `id_facture` : Identifiant de la facture (clé étrangère vers `factures`).
   - **Clé primaire composée** : (`id_commentaire`, `id_facture`).

## Utilisateurs de la base de données

### Utilisateur pour ajouter des données

- **Nom d'utilisateur** : `user_add`
- **Mot de passe** : `password_add`
- **Privilèges** : Peut ajouter des données dans toutes les tables de la base de données.

### Utilisateur pour lire les données

- **Nom d'utilisateur** : `user_read`
- **Mot de passe** : `password_read`
- **Privilèges** : Peut lire les données de toutes les tables de la base de données.

### Utilisateur pour modifier des données

- **Nom d'utilisateur** : `user_modify`
- **Mot de passe** : `password_modify`
- **Privilèges** : Peut modifier les données dans toutes les tables de la base de données.

## Instructions d'installation

1. Créez la base de données en exécutant le script SQL suivant :
    ```sql
    CREATE DATABASE OpenRelance;
    USE OpenRelance;
    ```

2. Créez les tables en exécutant le script SQL suivant :
    ```sql
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
        mail_contactes_clients VARCHAR(255),
        telphone_contactes_clients VARCHAR(20),
        commentaire_contactes_clients TEXT,
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
    ```

3. Créez les utilisateurs de la base de données et attribuez les privilèges en exécutant le script SQL suivant :
    ```sql
    CREATE USER 'user_add'@'localhost' IDENTIFIED BY 'password_add';
    GRANT INSERT ON OpenRelance.* TO 'user_add'@'localhost';

    CREATE USER 'user_read'@'localhost' IDENTIFIED BY 'password_read';
    GRANT SELECT ON OpenRelance.* TO 'user_read'@'localhost';

    CREATE USER 'user_modify'@'localhost' IDENTIFIED BY 'password_modify';
    GRANT UPDATE ON OpenRelance.* TO 'user_modify'@'localhost';

    FLUSH PRIVILEGES;
    ```

## Notes

- Assurez-vous de remplacer `'localhost'` par le nom d'hôte approprié si les utilisateurs se connectent à partir d'un autre hôte.
- Remplacez les mots de passe par ceux que vous souhaitez utiliser.

## Contributeur

- [Dylan PERINETTI](https://github.com/dylanPerinetti/)

## Liens utiles

- [Documentation MySQL](https://dev.mysql.com/doc/)
