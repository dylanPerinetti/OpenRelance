<?php
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');
$data = json_decode(file_get_contents('php://input'), true);
$client_id = $data['client_id'];

$sql = "SELECT cl.*, u.initial_user_open_relance 
        FROM clients cl
        JOIN user_open_relance u ON cl.id_user_open_relance = u.id
        WHERE cl.id = :client_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':client_id', $client_id);
$stmt->execute();

$client = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($client);
?>
