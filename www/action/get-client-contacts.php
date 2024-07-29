<?php
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');
$data = json_decode(file_get_contents('php://input'), true);
$client_id = $data['client_id'];

$sql = "SELECT 
            c.*, 
            cl.numeros_parma, 
            cl.nom_client 
        FROM 
            contactes_clients c 
        JOIN 
            clients cl 
        ON 
            c.id_clients = cl.id 
        WHERE 
            c.id_clients = :client_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':client_id', $client_id);
$stmt->execute();

$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($contacts);
?>
