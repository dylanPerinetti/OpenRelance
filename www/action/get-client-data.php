<?php
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');

$sql = "
    SELECT 
        c.id,
        c.numeros_parma,
        c.nom_client,
        COUNT(f.id) AS nb_factures_non_payees,
        COALESCE(SUM(f.montant_reste_a_payer), 0) AS montant_du
    FROM 
        clients c
    LEFT JOIN 
        factures f ON c.id = f.id_clients 
        AND f.date_echeance_payment < DATE_SUB(CURDATE(), INTERVAL 1 DAY) 
        AND f.montant_reste_a_payer != 0
    GROUP BY 
        c.id, c.numeros_parma, c.nom_client";

$stmt = $conn->prepare($sql);
$stmt->execute();

$response = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalImpayes = 0;
foreach ($response as $client) {
    $totalImpayes += $client['montant_du'];
}

echo json_encode(['clients' => $response, 'totalImpayes' => $totalImpayes]);
?>
