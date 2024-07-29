<?php
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');
$data = json_decode(file_get_contents('php://input'), true);
$client_id = $data['client_id'];

$sql = "SELECT * FROM factures WHERE id_clients = :client_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':client_id', $client_id);
$stmt->execute();

$factures = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($factures);
?>
