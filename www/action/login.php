<?php
session_start();
require_once '../connexion/mysql-db-config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'psw', FILTER_SANITIZE_STRING);

    if (empty($email) || empty($password)) {
        log_error('Missing email or password.');
        echo 'Veuillez remplir tous les champs.';
        exit;
    }

    $conn = get_db_connection('read');

    $sql = 'SELECT id, nom_user_open_relance, prenom_user_open_relance, initial_user_open_relance, mot_de_passe, type_de_profil FROM user_open_relance WHERE email_user_open_relance = :email';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom_user_open_relance'];
        $_SESSION['user_prenom'] = $user['prenom_user_open_relance'];
        $_SESSION['user_initiales'] = $user['initial_user_open_relance'];
        $_SESSION['type_de_profil'] = $user['type_de_profil'];  // Stockage du type de profil
        log_nominal("User logged in: $email");
        header('Location: ../index.php');
        exit;
    } else {
        log_error("Failed login attempt for email: $email");
        echo 'Email ou mot de passe incorrect.';
    }
} else {
    log_error('Unsupported request method.');
    echo 'Méthode de requête non supportée.';
}
?>
