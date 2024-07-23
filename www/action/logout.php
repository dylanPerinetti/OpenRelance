<?php
session_start();
require_once '../connexion/mysql-db-config.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Enregistrez le message de déconnexion dans le fichier de log nominal
    log_nominal("User logged out: {$_SESSION['user_nom']} {$_SESSION['user_prenom']} (ID: {$_SESSION['user_id']})");

    // Détruisez la session pour déconnecter l'utilisateur
    session_destroy();

    // Redirigez vers la page d'accueil ou une autre page appropriée
    header('Location: ../index.php');
    exit;
} else {
    echo 'Aucun utilisateur connecté.';
}
?>
