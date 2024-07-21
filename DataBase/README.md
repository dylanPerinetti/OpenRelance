# OpenRelance Database

## Description

La base de données **OpenRelance** est conçue pour gérer les informations liées aux utilisateurs, clients, factures, commentaires et contacts clients d'une plateforme de relance de paiements.

## Structure de la base de données

### Tables

1. **user_open_relance**
   - `id` : Identifiant unique de l'utilisateur (auto-incrémenté).
   - `nom_user_open_relance` : Nom de l'utilisateur.
   - `prenom_user_open_relance` : Prénom de l'utilisateur.
   - `initial_user_open_relance` : Initiales de l'utilisateur (2 caractères).
   - `type_de_profil` : Type de profil de l'utilisateur (représenté par un entier).
   - `mot_de_passe` : Mot de passe de l'utilisateur.

2. **clients**
   - `id` : Identifiant unique du client (auto-incrémenté).
   - `nom_client` : Nom du client.
   - `numeros_parma` : Numéro unique du client.

3. **factures**
   - `id` : Identifiant unique de la facture (auto-incrémenté).
   - `numeros_de_facture` : Numéro de la facture (contenant des lettres et des chiffres).
   - `date_echeance_payment` : Date d'échéance du paiement.
   - `montant_facture` : Montant de la facture.
   - `id_entreprise` : Identifiant de l'entreprise cliente (clé étrangère vers `clients`).

4. **commentaires**
   - `id` : Identifiant unique du commentaire (auto-incrémenté).
   - `date_commentaire` : Date du commentaire (générée automatiquement).
   - `message_commentaire` : Contenu du commentaire.
   - `id_factures` : Identifiant de la facture associée (clé étrangère vers `factures`).

5. **contactes_clients**
   - `id` : Identifiant unique du contact client (auto-incrémenté).
   - `fonction_contactes_clients` : Fonction du contact client.
   - `nom_contactes_clients` : Nom du contact client.
   - `mail_contactes_clients` : Adresse email du contact client.
   - `telphone_contactes_clients` : Numéro de téléphone du contact client.
   - `id_entreprise` : Identifiant de l'entreprise cliente (clé étrangère vers `clients`).

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
        mot_de_passe VARCHAR(255) NOT NULL
    );

    CREATE TABLE clients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom_client VARCHAR(255) NOT NULL,
        numeros_parma VARCHAR(255) NOT NULL
    );

    CREATE TABLE factures (
        id INT AUTO_INCREMENT PRIMARY KEY,
        numeros_de_facture VARCHAR(255) NOT NULL,
        date_echeance_payment DATE NOT NULL,
        montant_facture DECIMAL(10, 2) NOT NULL,
        id_entreprise INT,
        FOREIGN KEY (id_entreprise) REFERENCES clients(id)
    );

    CREATE TABLE commentaires (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date_commentaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        message_commentaire TEXT NOT NULL,
        id_factures INT,
        FOREIGN KEY (id_factures) REFERENCES factures(id)
    );

    CREATE TABLE contactes_clients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fonction_contactes_clients VARCHAR(255) NOT NULL,
        nom_contactes_clients VARCHAR(255) NOT NULL,
        mail_contactes_clients VARCHAR(255) NOT NULL,
        telphone_contactes_clients VARCHAR(20) NOT NULL,
        id_entreprise INT,
        FOREIGN KEY (id_entreprise) REFERENCES clients(id)
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

- Dylan PERINETTI ([https://github.com/dylanPerinetti/](URL))

## Liens utiles

- [Documentation MySQL](https://dev.mysql.com/doc/)
