<?php
session_start();
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');

header('Content-Type: application/json');

$response = [];

try {
    $sql = "
        SELECT 
            f.id, 
            f.numeros_de_facture, 
            f.date_echeance_payment, 
            f.date_emission_facture, -- Ajouter la colonne ici
            f.montant_facture, 
            f.montant_reste_a_payer, 
            c.nom_client, 
            c.numeros_parma,
            f.id_user_open_relance
        FROM 
            factures f
        JOIN 
            clients c ON f.id_clients = c.id
    ";
    $stmt = $conn->query($sql);
    $factures = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($factures);
    log_nominal("Factures retrieved successfully");
} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    echo json_encode($response);
    log_error("PDOException: " . $e->getMessage());
}
?>
