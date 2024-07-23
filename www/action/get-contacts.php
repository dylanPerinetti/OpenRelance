<?php
session_start();
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');

header('Content-Type: application/json');

$response = [];

try {
    $stmt = $conn->query("SELECT * FROM contactes_clients");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($contacts);
    log_nominal("Contacts retrieved successfully");
} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    echo json_encode($response);
    log_error("PDOException: " . $e->getMessage());
}
?>
