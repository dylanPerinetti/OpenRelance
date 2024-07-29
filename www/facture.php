<?php
session_start();
include 'connexion/mysql-db-config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de facture invalide.";
    exit;
}

$facture_id = $_GET['id'];
$conn = get_db_connection('view');

// Récupération des détails de la facture
$sql = "SELECT f.*, c.nom_client, u.initial_user_open_relance
FROM factures f
JOIN clients c ON f.id_clients = c.id
JOIN user_open_relance u ON f.id_user_open_relance = u.id
WHERE f.id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $facture_id, PDO::PARAM_INT);
$stmt->execute();
$facture = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$facture) {
    echo "Facture non trouvée.";
    exit;
}

// Récupération des commentaires associés
$sql_comments = "SELECT c.date_commentaire, c.message_commentaire, u.initial_user_open_relance
FROM commentaires c
JOIN commentaires_factures cf ON c.id = cf.id_commentaire
JOIN user_open_relance u ON c.id_user_open_relance = u.id
WHERE cf.id_facture = :id";
$stmt_comments = $conn->prepare($sql_comments);
$stmt_comments->bindParam(':id', $facture_id, PDO::PARAM_INT);
$stmt_comments->execute();
$commentaires = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

// Récupération des contacts associés au client
$sql_contacts = "SELECT id, fonction_contactes_clients, nom_contactes_clients 
FROM contactes_clients 
WHERE id_clients = :id_clients";
$stmt_contacts = $conn->prepare($sql_contacts);
$stmt_contacts->bindParam(':id_clients', $facture['id_clients'], PDO::PARAM_INT);
$stmt_contacts->execute();
$contacts = $stmt_contacts->fetchAll(PDO::FETCH_ASSOC);

$page_name = 'Facture (' . $facture['numeros_de_facture'] . ')';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'static/head.php'; ?>
</head>
<body>
    <?php include 'static/navbar.php'; ?>
    <?php include 'widget/widget-facture.php'; ?>
    <?php include 'static/footer.php'; ?>
</body>
</html>
