<?php
session_start();
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$clientId = $data['id_client'];

$response = [];

try {
    $stmt = $conn->prepare("SELECT nom_client FROM clients WHERE id = ?");
    $stmt->execute([$clientId]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($client) {
        $response['nom_client'] = $client['nom_client'];
        log_nominal("Client name retrieved: ID $clientId, Name: {$client['nom_client']}");
    } else {
        $response['nom_client'] = 'Client non trouvé';
        log_error("Client not found: ID $clientId");
    }
} catch (PDOException $e) {
    $response['message'] = $e->getMessage();
    log_error("PDOException: " . $e->getMessage());
}

echo json_encode($response);
?>
