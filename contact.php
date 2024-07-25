<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}
$page_name = 'Contacts';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'static/head.php'?>
</head>
<body>
    <?php include 'static/navbar.php'?>
    <?php include 'widget/widget-contacts.php'?>
    <?php include 'static/footer.php'?>
</body>
</html>