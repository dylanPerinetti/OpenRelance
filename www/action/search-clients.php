<?php
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');
$data = json_decode(file_get_contents('php://input'), true);

$searchTerm = $data['search'];

$sql = "SELECT id, nom_client FROM clients WHERE nom_client LIKE :searchTerm";
$stmt = $conn->prepare($sql);
$searchTerm = '%' . $searchTerm . '%';
$stmt->bindParam(':searchTerm', $searchTerm);
$stmt->execute();

$clients = $stmt->fetchAll();

echo json_encode($clients);
?>
