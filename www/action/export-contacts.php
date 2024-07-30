<?php
session_start();
include '../connexion/mysql-db-config.php';

$readConn = get_db_connection('read');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=contacts.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Numéro Parma', 'Client', 'Nom', 'Fonction', 'Téléphone', 'Email']);

$sql = "
    SELECT cl.numeros_parma, cl.nom_client, c.nom_contactes_clients, c.fonction_contactes_clients, c.telphone_contactes_clients, c.mail_contactes_clients
    FROM contactes_clients c
    JOIN clients cl ON c.id_clients = cl.id
";
$stmt = $readConn->prepare($sql);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
?>
