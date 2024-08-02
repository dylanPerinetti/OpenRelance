<?php
require_once '../connexion/mysql-db-config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$factures = $input['factures'];
$relanceType = $input['relanceType'];
$relanceDate = $input['relanceDate'];
$contactId = $input['contactId'];
$commentaire = $input['commentaire'];
$userId = $input['userId'];

if (empty($factures) || empty($relanceType) || empty($relanceDate) || empty($userId)) {
    echo json_encode(['success' => false, 'message' => 'Les champs relanceType, relanceDate et userId sont requis.']);
    exit;
}

try {
    $pdo = get_db_connection('add');
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO relance_client (type_relance, date_relance, id_contact_client, commentaire, id_user_open_relance) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$relanceType, $relanceDate, $contactId, $commentaire, $userId]);
    $relanceId = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO relance_facture (id_relance_client, id_facture) VALUES (?, ?)");
    foreach ($factures as $factureId) {
        $stmt->execute([$relanceId, $factureId]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Erreur lors de l\'ajout de la relance : ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la relance.']);
}
?>
