<?php
session_start();
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('add');
$data = json_decode(file_get_contents('php://input'), true);

$nom_client = $data['nom_client'];
$numeros_parma = $data['numeros_parma'];
$id_user_open_relance = $_SESSION['user_id'];

$sql = "INSERT INTO clients (nom_client, numeros_parma, id_user_open_relance) VALUES (:nom_client, :numeros_parma, :id_user_open_relance)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':nom_client', $nom_client);
$stmt->bindParam(':numeros_parma', $numeros_parma);
$stmt->bindParam(':id_user_open_relance', $id_user_open_relance);

$response = ['success' => false];

try {
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['new_id'] = $conn->lastInsertId();
        log_nominal("New client added: ID {$response['new_id']}, Name: $nom_client, Parma Number: $numeros_parma, User ID: $id_user_open_relance");
    } else {
        log_error("Failed to add client: Name: $nom_client, Parma Number: $numeros_parma, User ID: $id_user_open_relance");
    }
} catch (PDOException $e) {
    log_error("PDOException: " . $e->getMessage());
}

echo json_encode($response);
?>
