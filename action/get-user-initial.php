<?php
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');
$data = json_decode(file_get_contents('php://input'), true);

$id_user = $data['id_user'];

$sql = "SELECT initial_user_open_relance FROM user_open_relance WHERE id = :id_user";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_user', $id_user);
$stmt->execute();

$response = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($response);
?>
