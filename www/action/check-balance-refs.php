<?php
session_start();
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');
$data = json_decode(file_get_contents('php://input'), true);
$references = $data['references'];

$response = [];

try {
    $stmt = $conn->prepare("SELECT numeros_de_facture FROM factures WHERE numeros_de_facture IN (" . implode(',', array_fill(0, count($references), '?')) . ")");
    $stmt->execute($references);
    $existingRefs = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $response = $existingRefs;
} catch (PDOException $e) {
    log_error("PDOException: " . $e->getMessage());
} catch (Exception $e) {
    log_error("Exception: " . $e->getMessage());
}

echo json_encode($response);
?>
