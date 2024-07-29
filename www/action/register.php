<?php
require_once '../connexion/mysql-db-config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Assainir et valider les champs
    $nom = htmlspecialchars($_POST['nom'], ENT_QUOTES, 'UTF-8');
    $prenom = htmlspecialchars($_POST['prenom'], ENT_QUOTES, 'UTF-8');
    $initiales = htmlspecialchars($_POST['initiales'], ENT_QUOTES, 'UTF-8');
    $type_de_profil = filter_input(INPUT_POST, 'type_de_profil', FILTER_VALIDATE_INT);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['psw']; // Pas besoin d'assainir les mots de passe

    // Vérifier si les champs sont vides ou invalides
    if (empty($nom) || empty($prenom) || empty($initiales) || $type_de_profil === false || $email === false || empty($password)) {
        log_error('Missing or invalid registration fields.');
        echo 'Veuillez remplir tous les champs correctement.';
        exit;
    }

    // Hasher le mot de passe
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);

    // Connexion à la base de données
    $conn = get_db_connection('add');

    // Préparer et exécuter la requête SQL
    $sql = 'INSERT INTO user_open_relance (nom_user_open_relance, prenom_user_open_relance, initial_user_open_relance, type_de_profil, email_user_open_relance, mot_de_passe) VALUES (:nom, :prenom, :initiales, :type_de_profil, :email, :mot_de_passe)';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':initiales', $initiales);
    $stmt->bindParam(':type_de_profil', $type_de_profil);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mot_de_passe', $password_hashed);

    // Exécuter la requête et vérifier le succès
    if ($stmt->execute()) {
        log_nominal("User registered: $email");
        echo 'Inscription réussie!';
    } else {
        log_error('Registration error for email: ' . $email);
        echo 'Erreur lors de l\'inscription.';
    }
} else {
    log_error('Unsupported request method for registration.');
    echo 'Méthode de requête non supportée.';
}
?>
