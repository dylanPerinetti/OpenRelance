<?php
session_start();
include '../connexion/mysql-db-config.php';

$modifyConn = get_db_connection('modify');
$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'];
$fonction = $data['fonction_contactes_clients'];
$nom = $data['nom_contactes_clients'];
$email = $data['mail_contactes_clients'];
$telephone = $data['telphone_contactes_clients'];

$response = ['success' => false];

try {
    $stmt = $modifyConn->prepare("
        UPDATE contactes_clients 
        SET fonction_contactes_clients = :fonction, nom_contactes_clients = :nom, mail_contactes_clients = :email, telphone_contactes_clients = :telephone 
        WHERE id = :id
    ");
    $stmt->execute([
        ':fonction' => $fonction,
        ':nom' => $nom,
        ':email' => $email,
        ':telephone' => $telephone,
        ':id' => $id
    ]);

    $response['success'] = true;
} catch (PDOException $e) {
    log_error("PDOException: " . $e->getMessage());
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
