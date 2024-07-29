<?php
session_start();
require_once '../connexion/mysql-db-config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Assainir et valider l'email
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    // Utiliser htmlspecialchars() si vous devez afficher des données dans le HTML, mais pas pour les mots de passe
    $password = $_POST['psw']; // Assigner directement pour les mots de passe

    // Vérifier si les champs sont vides
    if (empty($email) || empty($password)) {
        log_error('Missing email or password.');
        echo 'Veuillez remplir tous les champs.';
        exit;
    }

    // Connexion à la base de données
    $conn = get_db_connection('read');

    // Préparer et exécuter la requête SQL
    $sql = 'SELECT id, nom_user_open_relance, prenom_user_open_relance, initial_user_open_relance, mot_de_passe, type_de_profil FROM user_open_relance WHERE email_user_open_relance = :email';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    // Vérifier si l'utilisateur existe et si le mot de passe est correct
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
