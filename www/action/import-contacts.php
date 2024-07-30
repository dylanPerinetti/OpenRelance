<?php
session_start();
include '../connexion/mysql-db-config.php';

// Utiliser une connexion de lecture pour récupérer l'ID du client
$readConn = get_db_connection('read');
// Utiliser une connexion de modification pour insérer le contact
$addConn = get_db_connection('add');

$data = json_decode(file_get_contents('php://input'), true);
$contacts = $data['contacts'];

$response = ['success' => false];

try {
    $addConn->beginTransaction();

    $stmtInsertContact = $addConn->prepare("
        INSERT INTO contactes_clients (fonction_contactes_clients, nom_contactes_clients, mail_contactes_clients, telphone_contactes_clients, id_clients)
        VALUES (:fonction_contactes_clients, :nom_contactes_clients, :mail_contactes_clients, :telphone_contactes_clients, :id_clients)
    ");

    foreach ($contacts as $contact) {
        // Get client ID
        $stmtGetClient = $readConn->prepare("SELECT id FROM clients WHERE numeros_parma = :numeros_parma");
        $stmtGetClient->execute([':numeros_parma' => $contact['numeros_parma']]);
        $client = $stmtGetClient->fetch(PDO::FETCH_ASSOC);

        if ($client) {
            $stmtInsertContact->execute([
                ':fonction_contactes_clients' => $contact['fonction_contactes_clients'],
                ':nom_contactes_clients' => $contact['nom_contactes_clients'],
                ':mail_contactes_clients' => $contact['mail_contactes_clients'],
                ':telphone_contactes_clients' => $contact['telphone_contactes_clients'],
                ':id_clients' => $client['id']
            ]);
        }
    }

    $addConn->commit();
    $response['success'] = true;
} catch (PDOException $e) {
    $addConn->rollBack();
    log_error("PDOException: " . $e->getMessage());
    $response['message'] = $e->getMessage();
} catch (Exception $e) {
    $addConn->rollBack();
    log_error("Exception: " . $e->getMessage());
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
