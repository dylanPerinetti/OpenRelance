<?php
require_once '../connexion/mysql-db-config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $date = $data['date'];

    try {
        $pdo = get_db_connection('read');
        $stmt = $pdo->prepare('
            SELECT 
                relance_client.id AS relance_id,
                relance_client.type_relance,
                relance_client.commentaire,
                clients.nom_client,
                clients.numeros_parma,
                contactes_clients.nom_contactes_clients AS contact_client,
                factures.id AS facture_id,
                factures.numeros_de_facture,
                factures.montant_facture,
                factures.date_echeance_payment
            FROM 
                relance_client
            LEFT JOIN 
                contactes_clients ON relance_client.id_contact_client = contactes_clients.id
            LEFT JOIN 
                clients ON contactes_clients.id_clients = clients.id
            LEFT JOIN 
                relance_facture ON relance_client.id = relance_facture.id_relance_client
            LEFT JOIN 
                factures ON relance_facture.id_facture = factures.id
            WHERE 
                relance_client.date_relance = :date
        ');
        $stmt->execute(['date' => $date]);
        $relances = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($relances);
    } catch (PDOException $e) {
        log_error('Erreur lors de la récupération des relances : ' . $e->getMessage());
        echo json_encode([]);
    }
}
?>
