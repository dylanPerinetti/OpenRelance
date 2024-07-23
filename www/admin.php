<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/global.css">
    <link rel="icon" type="image/x-icon" href="/img/favicon.ico">
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Admin</title>
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