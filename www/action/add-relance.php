<?php
session_start();
include '../connexion/mysql-db-config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$id_factures = $data['id_factures'];
$type_relance = $data['type_relance'];
$date_relance = $data['date_relance'];
$id_contact_client = $data['id_contact_client'];
$id_user_open_relance = $_SESSION['user_id'];

if (empty($type_relance) || empty($date_relance) || !is_numeric($id_factures) || !is_numeric($id_contact_client)) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$conn = get_db_connection('add');

try {
    // Démarrer la transaction
    $conn->beginTransaction();

    // Insérer la nouvelle relance
    $sql = "INSERT INTO relance_client (type_relance, date_relance, id_contact_client, id_user_open_relance) 
            VALUES (:type_relance, :date_relance, :id_contact_client, :id_user_open_relance)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':type_relance', $type_relance);
    $stmt->bindParam(':date_relance', $date_relance);
    $stmt->bindParam(':id_contact_client', $id_contact_client);
    $stmt->bindParam(':id_user_open_relance', $id_user_open_relance);

    if ($stmt->execute()) {
        // Obtenir l'ID de la relance insérée
        $id_relance_client = $conn->lastInsertId();

        // Insérer dans la table relance_facture
        $sql_relance_facture = "INSERT INTO relance_facture (id_relance_client, id_facture) VALUES (:id_relance_client, :id_facture)";
        $stmt_relance_facture = $conn->prepare($sql_relance_facture);
        $stmt_relance_facture->bindParam(':id_relance_client', $id_relance_client);
        $stmt_relance_facture->bindParam(':id_facture', $id_factures);

        if ($stmt_relance_facture->execute()) {
            // Valider la transaction
            $conn->commit();
            echo json_encode(['success' => true]);
        } else {
            // Annuler la transaction en cas d'échec
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => 'Échec de l\'association de la relance à la facture.']);
        }
    } else {
        // Annuler la transaction en cas d'échec
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Échec de l\'exécution de la requête.']);
    }
} catch (PDOException $e) {
    // Annuler la transaction en cas d'exception
    $conn->rollBack();
    error_log("PDOException: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Exception : ' . $e->getMessage()]);
}
?>
