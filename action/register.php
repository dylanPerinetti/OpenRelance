<?php
require_once '../connexion/mysql-db-config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
    $initiales = filter_input(INPUT_POST, 'initiales', FILTER_SANITIZE_STRING);
    $type_de_profil = filter_input(INPUT_POST, 'type_de_profil', FILTER_SANITIZE_NUMBER_INT);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'psw', FILTER_SANITIZE_STRING);

    if (empty($nom) || empty($prenom) || empty($initiales) || empty($type_de_profil) || empty($email) || empty($password)) {
        log_error('Missing registration fields.');
        echo 'Veuillez remplir tous les champs.';
        exit;
    }

    $password_hashed = password_hash($password, PASSWORD_BCRYPT);

    $conn = get_db_connection('add');

    $sql = 'INSERT INTO user_open_relance (nom_user_open_relance, prenom_user_open_relance, initial_user_open_relance, type_de_profil, email_user_open_relance, mot_de_passe) VALUES (:nom, :prenom, :initiales, :type_de_profil, :email, :mot_de_passe)';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':initiales', $initiales);
    $stmt->bindParam(':type_de_profil', $type_de_profil);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mot_de_passe', $password_hashed);

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
