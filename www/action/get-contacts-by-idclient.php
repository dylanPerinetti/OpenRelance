<?php
require_once '../connexion/mysql-db-config.php';

header('Content-Type: application/json');

$client_id = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;

if ($client_id === 0) {
    echo json_encode([]);
    exit;
}

try {
    $pdo = get_db_connection('read');
    $stmt = $pdo->prepare("SELECT id, nom_contactes_clients, mail_contactes_clients FROM contactes_clients WHERE id_clients = ?");
    $stmt->execute([$client_id]);
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($contacts);
} catch (PDOException $e) {
    log_error('Erreur lors de la récupération des contacts : ' . $e->getMessage());
    echo json_encode([]);
}
?>
