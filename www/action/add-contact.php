<?php
session_start();
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('add');
$data = json_decode(file_get_contents('php://input'), true);

$functionContact = $data['fonction_contactes_clients'];
$name = $data['nom_contactes_clients'];
$email = $data['mail_contactes_clients'];
$phone = $data['telphone_contactes_clients'];
$clientId = $data['id_clients'];
$id_user_open_relance = $_SESSION['user_id'];

$sql = "INSERT INTO contactes_clients (fonction_contactes_clients, nom_contactes_clients, mail_contactes_clients, telphone_contactes_clients, id_clients) VALUES (:fonction_contactes_clients, :nom_contactes_clients, :mail_contactes_clients, :telphone_contactes_clients, :id_clients)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':fonction_contactes_clients', $functionContact);
$stmt->bindParam(':nom_contactes_clients', $name);
$stmt->bindParam(':mail_contactes_clients', $email);
$stmt->bindParam(':telphone_contactes_clients', $phone);
$stmt->bindParam(':id_clients', $clientId);

$response = ['success' => false];

try {
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['new_id'] = $conn->lastInsertId();
        log_nominal("New contact added: ID {$response['new_id']}, Function: $functionContact, Name: $name, Email: $email, Phone: $phone, Client ID: $clientId, User ID: $id_user_open_relance");
    } else {
        log_error("Failed to add contact: Function: $functionContact, Name: $name, Email: $email, Phone: $phone, Client ID: $clientId, User ID: $id_user_open_relance");
    }
} catch (PDOException $e) {
    log_error("PDOException: " . $e->getMessage());
}

echo json_encode($response);
?>
