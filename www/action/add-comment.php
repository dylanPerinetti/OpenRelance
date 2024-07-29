<?php
session_start();
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('add'); // Utiliser le rôle 'add' pour les insertions

header('Content-Type: application/json');

$response = [];

$data = json_decode(file_get_contents('php://input'), true);
$factures = $data['factures'];
$comment = $data['comment'];
$userId = $data['userId'];

try {
    $conn->beginTransaction();

    // Insertion du commentaire
    $stmt = $conn->prepare("INSERT INTO commentaires (message_commentaire, id_user_open_relance) VALUES (:comment, :userId)");
    $stmt->bindParam(':comment', $comment);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $commentId = $conn->lastInsertId();

    // Préparation de la requête pour lier les commentaires aux factures
    $stmtInsert = $conn->prepare("INSERT INTO commentaires_factures (id_commentaire, id_facture) VALUES (:commentId, :factureId)");

    foreach ($factures as $factureId) {
        $stmtInsert->bindParam(':commentId', $commentId);
        $stmtInsert->bindParam(':factureId', $factureId);
        $stmtInsert->execute();
    }

    $conn->commit();
    $response['success'] = true;
    $response['message'] = 'Commentaire ajouté avec succès';
} catch (PDOException $e) {
    $conn->rollBack();
    $response['success'] = false;
    $response['message'] = 'Erreur lors de l\'ajout du commentaire: ' . $e->getMessage();
    error_log("PDOException: " . $e->getMessage());
} catch (Exception $e) {
    $conn->rollBack();
    $response['success'] = false;
    $response['message'] = 'Erreur: ' . $e->getMessage();
}

echo json_encode($response);
?>
