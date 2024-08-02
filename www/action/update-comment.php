<?php
session_start();
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('add'); // Utiliser le rôle 'update' pour les mises à jour

header('Content-Type: application/json');

$response = [];

$data = json_decode(file_get_contents('php://input'), true);
$commentId = $data['id'];
$newMessage = $data['message'];
$userId = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("UPDATE commentaires SET message_commentaire = :message WHERE id = :commentId AND id_user_open_relance = :userId");
    $stmt->bindParam(':message', $newMessage);
    $stmt->bindParam(':commentId', $commentId);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['message'] = 'Aucune mise à jour effectuée.';
    }
} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = 'Erreur lors de la mise à jour du commentaire: ' . $e->getMessage();
    error_log("PDOException: " . $e->getMessage());
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Erreur: ' . $e->getMessage();
}

echo json_encode($response);
?>
