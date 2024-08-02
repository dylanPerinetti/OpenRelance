<?php
require '../connexion/mysql-db-config.php';

$data = json_decode(file_get_contents('php://input'), true);
$factureIds = $data['factures'];

if (empty($factureIds)) {
    echo json_encode(['success' => false, 'message' => 'Aucune facture sélectionnée.']);
    exit;
}

try {
    $conn = get_db_connection('add');
    $placeholders = implode(',', array_fill(0, count($factureIds), '?'));
    $sql = "UPDATE factures SET montant_reste_a_payer = 0 WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($factureIds);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Aucune facture mise à jour.']);
    }
} catch (PDOException $e) {
    log_error("Error updating factures: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour des factures.']);
}
?>
