<?php
session_start();
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');
$data = json_decode(file_get_contents('php://input'), true);
$references = $data['references'];

$response = [];

if (empty($references)) {
    echo json_encode($response);
    exit;
}

try {
    $placeholders = implode(',', array_fill(0, count($references), '?'));
    $stmt = $conn->prepare("SELECT numeros_de_facture, montant_reste_a_payer FROM factures WHERE numeros_de_facture IN ($placeholders)");
    $stmt->execute($references);
    $existingRefs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = $existingRefs;
} catch (PDOException $e) {
    log_error("PDOException: " . $e->getMessage());
} catch (Exception $e) {
    log_error("Exception: " . $e->getMessage());
}

echo json_encode($response);
?>
