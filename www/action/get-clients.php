<?php
include '../connexion/mysql-db-config.php';

// Connexion à la base de données
$conn = get_db_connection('read');

// Récupération des clients
$sql = "SELECT * FROM clients";
$stmt = $conn->query($sql);

$clients = [];

if ($stmt->rowCount() > 0) {
    while($row = $stmt->fetch()) {
        $clients[] = $row;
    }
}

// Fermer la connexion
$conn = null;

// Retourner les données en JSON
header('Content-Type: application/json');
echo json_encode($clients);
?>
