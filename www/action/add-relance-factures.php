<?php
header('Content-Type: application/json');
include '../config.php';

$input = json_decode(file_get_contents('php://input'), true);
$factures = $input['factures'];
$relanceType = $input['relanceType'];
$relanceDate = $input['relanceDate'];
$contactId = $input['contactId'];
$userId = $input['userId'];

if (empty($factures) || empty($relanceType) || empty($relanceDate) || empty($contactId) || empty($userId)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis.']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO relance_client (type_relance, date_relance, id_contact_client, id_user_open_relance) VALUES (?, ?, ?, ?)");
    $stmt->execute([$relanceType, $relanceDate, $contactId, $userId]);
    $relanceId = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO relance_facture (id_relance_client, id_facture) VALUES (?, ?)");
    foreach ($factures as $factureId) {
        $stmt->execute([$relanceId, $factureId]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la relance.']);
}
?>
