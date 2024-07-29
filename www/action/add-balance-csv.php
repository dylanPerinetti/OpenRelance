<?php
session_start();
include '../connexion/mysql-db-config.php';

$user_id = htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8');

$readConn = get_db_connection('read'); // Connexion pour la lecture
$addConn = get_db_connection('add'); // Connexion pour l'ajout
$data = json_decode(file_get_contents('php://input'), true);
$rows = $data['data'];

$response = ['success' => false];

try {
    $addConn->beginTransaction();

    // Récupérer tous les clients existants
    $existingClients = [];
    $stmt = $readConn->prepare("SELECT nom_client, id FROM clients");
    $stmt->execute();
    foreach ($stmt as $row) {
        $existingClients[strtoupper($row['nom_client'])] = $row['id'];
    }

    // Récupérer toutes les factures existantes
    $existingFactures = [];
    $stmt = $readConn->prepare("SELECT numeros_de_facture FROM factures");
    $stmt->execute();
    foreach ($stmt as $row) {
        $existingFactures[$row['numeros_de_facture']] = true;
    }

    // Préparer les instructions SQL
    $stmtInsertClient = $addConn->prepare("INSERT INTO clients (nom_client, numeros_parma, id_user_open_relance) VALUES (:nom_client, :numeros_parma, :id_user_open_relance)");
    $stmtInsertFacture = $addConn->prepare("INSERT INTO factures (numeros_de_facture, date_emission_facture, date_echeance_payment, montant_facture, montant_reste_a_payer, id_clients, id_user_open_relance) VALUES (:numeros_de_facture, :date_emission_facture, :date_echeance_payment, :montant_facture, :montant_reste_a_payer, :id_clients, :id_user_open_relance)");

    foreach ($rows as $row) {
        $customerNumber = $row['customerNumber'];
        $customerName = strtoupper($row['customerName']);
        $documentDate = $row['documentDate'];
        $reference = $row['reference'];
        $dueDate = $row['dueDate'];
        $amount = $row['amount'];

        if (!isset($existingClients[$customerName])) {
            // Ajouter un nouveau client s'il n'existe pas
            $stmtInsertClient->execute([
                ':nom_client' => $customerName,
                ':numeros_parma' => $customerNumber,
                ':id_user_open_relance' => $user_id
            ]);
            $existingClients[$customerName] = $addConn->lastInsertId();
        }

        // Vérifier si la facture existe déjà
        if (!isset($existingFactures[$reference])) {
            $clientId = $existingClients[$customerName];

            // Ajouter la facture associée au client
            $stmtInsertFacture->execute([
                ':numeros_de_facture' => $reference,
                ':date_emission_facture' => $documentDate,
                ':date_echeance_payment' => $dueDate,
                ':montant_facture' => $amount,
                ':montant_reste_a_payer' => $amount,
                ':id_clients' => $clientId,
                ':id_user_open_relance' => $user_id
            ]);
            $existingFactures[$reference] = true; // Marquer cette facture comme existante
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
