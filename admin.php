<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}
$page_name = 'Admin Page';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'static/head.php'?>
</head>
<body>
    <?php include 'static/navbar.php'?>
    <h2>Test Inscription</h2>
    <form action="action/register.php" method="post">
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" required><br><br>

        <label for="prenom">Prénom:</label>
        <input type="text" id="prenom" name="prenom" required><br><br>

        <label for="initiales">Initiales:</label>
        <input type="text" id="initiales" name="initiales" required><br><br>

        <label for="type_de_profil">Type de Profil:</label>
        <input type="number" id="type_de_profil" name="type_de_profil" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="psw">Mot de passe:</label>
        <input type="password" id="psw" name="psw" required><br><br>

        <button type="submit">S'inscrire</button>
    </form>
    <?php include 'static/footer.php'?>
</body>
</html>