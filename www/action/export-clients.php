<?php
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('select');
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="clients.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, array('Nom', 'Numéro Parma'));

$sql = "SELECT nom_client, numeros_parma FROM clients";
foreach ($conn->query($sql) as $row) {
    fputcsv($output, $row);
}

fclose($output);
?>
