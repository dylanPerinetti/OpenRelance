<?php
session_start();
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('add');
$data = json_decode(file_get_contents('php://input'), true);

$response = ['success' => false];

try {
    if (!isset($data['numeros_de_facture']) || !isset($data['date_emission_facture']) || !isset($data['date_echeance_payment']) || 
        !isset($data['montant_facture']) || !isset($data['montant_reste_a_payer']) || !isset($data['id_clients'])) {
        throw new Exception("Missing required fields");
    }

    $numeros_de_facture = htmlspecialchars($data['numeros_de_facture'], ENT_QUOTES, 'UTF-8');
    $date_emission_facture = htmlspecialchars($data['date_emission_facture'], ENT_QUOTES, 'UTF-8');
    $date_echeance_payment = htmlspecialchars($data['date_echeance_payment'], ENT_QUOTES, 'UTF-8');
    $montant_facture = htmlspecialchars($data['montant_facture'], ENT_QUOTES, 'UTF-8');
    $montant_reste_a_payer = htmlspecialchars($data['montant_reste_a_payer'], ENT_QUOTES, 'UTF-8');
    $id_clients = htmlspecialchars($data['id_clients'], ENT_QUOTES, 'UTF-8');
    $id_user_open_relance = $_SESSION['user_id'];

    $sql = "INSERT INTO factures (numeros_de_facture, date_emission_facture, date_echeance_payment, montant_facture, montant_reste_a_payer, id_clients, id_user_open_relance) 
            VALUES (:numeros_de_facture, :date_emission_facture, :date_echeance_payment, :montant_facture, :montant_reste_a_payer, :id_clients, :id_user_open_relance)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':numeros_de_facture', $numeros_de_facture);
    $stmt->bindParam(':date_emission_facture', $date_emission_facture);
    $stmt->bindParam(':date_echeance_payment', $date_echeance_payment);
    $stmt->bindParam(':montant_facture', $montant_facture);
    $stmt->bindParam(':montant_reste_a_payer', $montant_reste_a_payer);
    $stmt->bindParam(':id_clients', $id_clients);
    $stmt->bindParam(':id_user_open_relance', $id_user_open_relance);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['new_id'] = $conn->lastInsertId();
        log_nominal("New invoice added: ID {$response['new_id']}, Invoice Number: $numeros_de_facture, Emission Date: $date_emission_facture, Due Date: $date_echeance_payment, Amount: $montant_facture, Remaining Amount: $montant_reste_a_payer, Client ID: $id_clients, User ID: $id_user_open_relance");
    } else {
        throw new Exception("Failed to add invoice");
    }
} catch (PDOException $e) {
    log_error("PDOException: " . $e->getMessage());
    $response['message'] = "Erreur lors de l'ajout de la facture.";
} catch (Exception $e) {
    log_error("Exception: " . $e->getMessage());
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
