<?php
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');
$data = json_decode(file_get_contents('php://input'), true);

$numeros_parma = $data['numeros_parma'];

$sql = "SELECT COUNT(*) FROM clients WHERE numeros_parma = :numeros_parma";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':numeros_parma', $numeros_parma);
$stmt->execute();

$response = ['exists' => $stmt->fetchColumn() > 0];

echo json_encode($response);
?>
