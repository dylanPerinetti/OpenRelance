<?php
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');
$data = json_decode(file_get_contents('php://input'), true);
$search = $data['search'];

$sql = "SELECT id, nom_client, numeros_parma FROM clients WHERE numeros_parma LIKE :search";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':search', '%' . $search . '%');
$stmt->execute();

$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($clients);
?>
