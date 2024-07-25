<?php
session_start();
include '../connexion/mysql-db-config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$id_factures = $data['id_factures'];
$message_commentaire = $data['message_commentaire'];
$id_user_open_relance = $_SESSION['user_id'];

if (empty($message_commentaire) || !is_numeric($id_factures)) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$conn = get_db_connection('add');
$sql = "INSERT INTO commentaires (message_commentaire, id_factures, id_user_open_relance) VALUES (:message_commentaire, :id_factures, :id_user_open_relance)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':message_commentaire', $message_commentaire);
$stmt->bindParam(':id_factures', $id_factures);
$stmt->bindParam(':id_user_open_relance', $id_user_open_relance);

$response = ['success' => false];

try {
    if ($stmt->execute()) {
        $response['success'] = true;
    }
} catch (PDOException $e) {
    error_log("PDOException: " . $e->getMessage());
}

echo json_encode($response);
?>
