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
$sql = "INSERT INTO relance_client (type_relance, date_relance, id_contact_client, id_user_open_relance) 
        VALUES (:type_relance, :date_relance, :id_contact_client, :id_user_open_relance)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':type_relance', $type_relance);
$stmt->bindParam(':date_relance', $date_relance);
$stmt->bindParam(':id_contact_client', $id_contact_client);
$stmt->bindParam(':id_user_open_relance', $id_user_open_relance);

$response = ['success' => false];

try {
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Échec de l\'exécution de la requête.';
    }
} catch (PDOException $e) {
    error_log("PDOException: " . $e->getMessage());
    $response['message'] = 'Exception : ' . $e->getMessage();
}

echo json_encode($response);
?>