<?php
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');
$sql = "SELECT 
            c.numeros_parma, 
            c.nom_client,
            c.id, 
            SUM(f.montant_reste_a_payer) AS montant_du 
        FROM 
            clients c
        LEFT JOIN 
            factures f ON c.id = f.id_clients
        GROUP BY 
            c.id";

$stmt = $conn->prepare($sql);
$stmt->execute();

$response = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($response);
?>
