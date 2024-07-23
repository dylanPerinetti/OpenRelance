<?php
session_start();
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('add');
$data = json_decode(file_get_contents('php://input'), true);

$numeros_de_facture = $data['numeros_de_facture'];
$date_echeance_payment = $data['date_echeance_payment'];
$montant_facture = $data['montant_facture'];
$montant_reste_a_payer = $data['montant_reste_a_payer'];
$id_clients = $data['id_clients'];
$id_user_open_relance = $_SESSION['user_id'];

$sql = "INSERT INTO factures (numeros_de_facture, date_echeance_payment, montant_facture, montant_reste_a_payer, id_clients, id_user_open_relance) VALUES (:numeros_de_facture, :date_echeance_payment, :montant_facture, :montant_reste_a_payer, :id_clients, :id_user_open_relance)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':numeros_de_facture', $numeros_de_facture);
$stmt->bindParam(':date_echeance_payment', $date_echeance_payment);
$stmt->bindParam(':montant_facture', $montant_facture);
$stmt->bindParam(':montant_reste_a_payer', $montant_reste_a_payer);
$stmt->bindParam(':id_clients', $id_clients);
$stmt->bindParam(':id_user_open_relance', $id_user_open_relance);

$response = ['success' => false];

try {
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['new_id'] = $conn->lastInsertId();
        log_nominal("New invoice added: ID {$response['new_id']}, Invoice Number: $numeros_de_facture, Due Date: $date_echeance_payment, Amount: $montant_facture, Remaining Amount: $montant_reste_a_payer, Client ID: $id_clients, User ID: $id_user_open_relance");
    } else {
        log_error("Failed to add invoice: Invoice Number: $numeros_de_facture, Due Date: $date_echeance_payment, Amount: $montant_facture, Remaining Amount: $montant_reste_a_payer, Client ID: $id_clients, User ID: $id_user_open_relance");
    }
} catch (PDOException $e) {
    log_error("PDOException: " . $e->getMessage());
}

echo json_encode($response);
?>
