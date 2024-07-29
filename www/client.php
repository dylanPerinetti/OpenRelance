<?php
session_start();
include 'connexion/mysql-db-config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID client invalide.";
    exit;
}

$client_id = $_GET['id']; // Assurez-vous d'obtenir l'ID du client à partir de l'URL ou d'une autre source

// Récupérer le nom du client
$conn = get_db_connection('read');
$sql = "SELECT nom_client,numeros_parma FROM clients WHERE id = :client_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':client_id', $client_id);
$stmt->execute();
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    echo "Client non trouvé.";
    exit;
}

$client_name = $client['nom_client'];
$client_parma = $client['numeros_parma'];
$page_name = '(' . $client_parma . ') ' . $client_name;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'static/head.php'?>
    <title><?php echo htmlspecialchars($page_name); ?></title>
</head>
<body>
    <?php include 'static/navbar.php'?>
    <?php include 'widget/widget-client.php'?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
    <?php include 'static/footer.php'?>
</body>
</html>
