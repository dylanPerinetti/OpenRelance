<?php
require_once '../connexion/mysql-db-config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $clientId = $data['client_id'];

    try {
        $pdo = get_db_connection('read');
        $stmt = $pdo->prepare('
            SELECT 
                relance_client.id AS relance_id,
                relance_client.type_relance,
                relance_client.date_relance,
                factures.numeros_de_facture,
                factures.montant_facture,
                factures.date_echeance_payment
            FROM 
                relance_client
            LEFT JOIN 
                relance_facture ON relance_client.id = relance_facture.id_relance_client
            LEFT JOIN 
                factures ON relance_facture.id_facture = factures.id
            WHERE 
                factures.id_clients = :client_id
        ');
        $stmt->execute(['client_id' => $clientId]);
        $relances = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!isset($relances[$row['relance_id']])) {
                $relances[$row['relance_id']] = [
                    'type_relance' => $row['type_relance'],
                    'date_relance' => $row['date_relance'],
                    'factures' => []
                ];
            }
            $relances[$row['relance_id']]['factures'][] = [
                'numeros_de_facture' => $row['numeros_de_facture'],
                'montant_facture' => $row['montant_facture']
            ];
        }
        echo json_encode(array_values($relances));
    } catch (PDOException $e) {
        log_error('Erreur lors de la récupération des relances : ' . $e->getMessage());
        echo json_encode([]);
    }
}
?>
