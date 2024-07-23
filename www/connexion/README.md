# Configuration de la Base de Données

## Description

Ce répertoire contient les fichiers de configuration nécessaires pour se connecter à la base de données **OpenRelance**. La base de données est utilisée pour gérer les informations liées aux utilisateurs, clients, factures, commentaires et contacts clients de la plateforme OpenRelance.

## Fichiers

### 1. `mysql-db-config.php`

Ce fichier contient la configuration pour se connecter à la base de données MySQL, ainsi que des fonctions pour gérer les connexions en fonction des rôles (lecture, ajout, modification) et pour enregistrer les logs des erreurs et des opérations nominales.

#### Contenu principal :

- **Constantes de connexion :** Définit les constantes pour les paramètres de connexion à la base de données.
- **Fonction `get_db_connection` :** Retourne une instance de connexion PDO en fonction du rôle spécifié.
- **Fonction `log_error` :** Enregistre les erreurs dans un fichier de log situé dans `../log/error/`.
- **Fonction `log_nominal` :** Enregistre les opérations nominales dans un fichier de log situé dans `../log/nominal/`.

### 2. Exemple d'utilisation

#### Connexion à la base de données

```php
require_once 'mysql-db-config.php';

$conn = get_db_connection('read');
```

#### Enregistrement d'une erreur

```php
log_error('Message d\'erreur exemple');
```

#### Enregistrement d'une opération nominale

```php
log_nominal('Message nominal exemple');
```

## Instructions d'installation

1. Créez la base de données en exécutant le script SQL suivant :

    ```sql
    CREATE DATABASE OpenRelance;
    USE OpenRelance;
    ```

2. Créez les tables nécessaires en exécutant le script SQL suivant :

    ```sql
    CREATE TABLE user_open_relance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom_user_open_relance VARCHAR(255) NOT NULL,
        prenom_user_open_relance VARCHAR(255) NOT NULL,
        initial_user_open_relance CHAR(2) NOT NULL,
        type_de_profil INT NOT NULL,
        email_user_open_relance VARCHAR(255) NOT NULL,
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

## Configuration

1. **Copiez le fichier `mysql-db-config.php`** dans le répertoire `connection` de votre projet.
2. **Modifiez les constantes de connexion** dans `mysql-db-config.php` pour qu'elles correspondent aux informations de votre serveur MySQL.
3. **Assurez-vous que les dossiers de log existent** : Créez les dossiers `log/error` et `log/nominal` si ce n'est pas déjà fait.

    ```bash
    mkdir -p ../log/error ../log/nominal
    ```

4. **Utilisez les fonctions de connexion et de log** dans vos scripts PHP en incluant `mysql-db-config.php`.

## Notes

- Assurez-vous de remplacer `'localhost'` par le nom d'hôte approprié si les utilisateurs se connectent à partir d'un autre hôte.
- Remplacez les mots de passe par ceux que vous souhaitez utiliser.

## Contributeurs

- [Dylan PERINETTI](https://github.com/dylanPerinetti/OpenRelance)

## Liens utiles

- [Documentation MySQL](https://dev.mysql.com/doc/)
```