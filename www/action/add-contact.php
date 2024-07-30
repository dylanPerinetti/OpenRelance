<?php
session_start();
include '../connexion/mysql-db-config.php';

// Utiliser une connexion de lecture pour récupérer l'ID du client
$readConn = get_db_connection('read');
// Utiliser une connexion d'ajout pour insérer le contact
$addConn = get_db_connection('add');

$data = json_decode(file_get_contents('php://input'), true);

$numeros_parma = $data['numeros_parma'];
$nom_client = strtoupper($data['nom_client']);
$nom_contact = $data['nom_contactes_clients'];
$fonction_contact = $data['fonction_contactes_clients'];
$email = $data['mail_contactes_clients'];
$telephone = $data['telphone_contactes_clients'];
$id_user_open_relance = $_SESSION['user_id'];

$response = ['success' => false];

try {
    // Récupérer l'ID du client
    $stmt = $readConn->prepare("SELECT id FROM clients WHERE numeros_parma = :numeros_parma OR nom_client = :nom_client");
    $stmt->execute([':numeros_parma' => $numeros_parma, ':nom_client' => $nom_client]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client) {
        $id_clients = $client['id'];

        // Ajouter le contact
        $stmtInsertContact = $addConn->prepare("
            INSERT INTO contactes_clients (fonction_contactes_clients, nom_contactes_clients, mail_contactes_clients, telphone_contactes_clients, id_clients, id_user_open_relance)
            VALUES (:fonction_contactes_clients, :nom_contactes_clients, :mail_contactes_clients, :telphone_contactes_clients, :id_clients, :id_user_open_relance)
        ");
        $stmtInsertContact->execute([
            ':fonction_contactes_clients' => $fonction_contact,
            ':nom_contactes_clients' => $nom_contact,
            ':mail_contactes_clients' => $email,
            ':telphone_contactes_clients' => $telephone,
            ':id_clients' => $id_clients,
            ':id_user_open_relance' => $id_user_open_relance
        ]);

        $response['success'] = true;
    } else {
        $response['message'] = "Client non trouvé.";
    }
} catch (PDOException $e) {
    log_error("PDOException: " . $e->getMessage());
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
