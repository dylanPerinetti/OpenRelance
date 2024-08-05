<?php
session_start();
include '../connexion/mysql-db-config.php';

$user_id = htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8');
$admin_user_id = 3; // L'utilisateur avec l'ID 3 pour les commentaires par défaut

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

    // Récupérer toutes les factures existantes, classées par client
    $existingFactures = [];
    $stmt = $readConn->prepare("SELECT id, numeros_de_facture, montant_facture, montant_reste_a_payer, id_clients FROM factures");
    $stmt->execute();
    foreach ($stmt as $row) {
        $client_id = $row['id_clients'];
        $existingFactures[$client_id][$row['numeros_de_facture']] = [
            'id' => $row['id'],
            'montant_facture' => $row['montant_facture'],
            'montant_reste_a_payer' => $row['montant_reste_a_payer']
        ];
    }

    // Préparer les instructions SQL
    $stmtInsertClient = $addConn->prepare("INSERT INTO clients (nom_client, numeros_parma, id_user_open_relance) VALUES (:nom_client, :numeros_parma, :id_user_open_relance)");
    $stmtInsertFacture = $addConn->prepare("INSERT INTO factures (numeros_de_facture, date_emission_facture, date_echeance_payment, montant_facture, montant_reste_a_payer, id_clients, id_user_open_relance) VALUES (:numeros_de_facture, :date_emission_facture, :date_echeance_payment, :montant_facture, :montant_reste_a_payer, :id_clients, :id_user_open_relance)");
    $stmtUpdateFacture = $addConn->prepare("UPDATE factures SET montant_reste_a_payer = :montant_reste_a_payer WHERE id = :id");
    $stmtInsertCommentaire = $addConn->prepare("INSERT INTO commentaires (message_commentaire, id_user_open_relance) VALUES (:message_commentaire, :id_user_open_relance)");
    $stmtLinkCommentaireFacture = $addConn->prepare("INSERT INTO commentaires_factures (id_commentaire, id_facture) VALUES (:id_commentaire, :id_facture)");

    // Traiter les lignes de la balance importée
    $importedFactures = [];
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

        $clientId = $existingClients[$customerName];
        $importedFactures[$reference] = $amount;

        // Vérifier si la facture existe déjà
        $factureExists = false;
        if (isset($existingFactures[$clientId][$reference])) {
            $factureExists = true;
            $factureId = $existingFactures[$clientId][$reference]['id'];
        } elseif (strpos($reference, 'ZC') !== 0) {
            foreach ($existingFactures as $clientFactures) {
                if (isset($clientFactures[$reference])) {
                    $factureExists = true;
                    $factureId = $clientFactures[$reference]['id'];
                    break;
                }
            }
        }

        if ($factureExists) {
            // Mettre à jour le montant restant à payer si la facture est présente dans la balance importée
            if ($existingFactures[$clientId][$reference]['montant_reste_a_payer'] != $amount) {
                $stmtUpdateFacture->execute([
                    ':montant_reste_a_payer' => $amount,
                    ':id' => $factureId
                ]);
                $stmtInsertCommentaire->execute([
                    ':message_commentaire' => "Facture mise à jour selon la balance importée",
                    ':id_user_open_relance' => $admin_user_id
                ]);
                $commentaireId = $addConn->lastInsertId();
                $stmtLinkCommentaireFacture->execute([
                    ':id_commentaire' => $commentaireId,
                    ':id_facture' => $factureId
                ]);
            }
        } else {
            // Ajouter une nouvelle facture
            $stmtInsertFacture->execute([
                ':numeros_de_facture' => $reference,
                ':date_emission_facture' => $documentDate,
                ':date_echeance_payment' => $dueDate,
                ':montant_facture' => $amount,
                ':montant_reste_a_payer' => $amount,
                ':id_clients' => $clientId,
                ':id_user_open_relance' => $user_id
            ]);
            $factureId = $addConn->lastInsertId();
            $existingFactures[$clientId][$reference] = [
                'id' => $factureId,
                'montant_facture' => $amount,
                'montant_reste_a_payer' => $amount
            ]; // Marquer cette facture comme existante
        }
    }

    // Mettre à jour les factures non présentes dans la balance importée
    foreach ($existingFactures as $clientFactures) {
        foreach ($clientFactures as $reference => $facture) {
            if (!isset($importedFactures[$reference]) && $facture['montant_reste_a_payer'] != 0) {
                $stmtUpdateFacture->execute([
                    ':montant_reste_a_payer' => 0,
                    ':id' => $facture['id']
                ]);
                $stmtInsertCommentaire->execute([
                    ':message_commentaire' => "Facture payée",
                    ':id_user_open_relance' => $admin_user_id
                ]);
                $commentaireId = $addConn->lastInsertId();
                $stmtLinkCommentaireFacture->execute([
                    ':id_commentaire' => $commentaireId,
                    ':id_facture' => $facture['id']
                ]);
            }
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
