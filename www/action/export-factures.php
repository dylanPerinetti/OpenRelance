<?php
session_start();
include '../connexion/mysql-db-config.php';

function fetch_factures($client, $status) {
    $pdo = get_db_connection('read');
    $query = "SELECT * FROM factures WHERE 1=1";
    $params = [];

    if (!empty($client)) {
        $query .= " AND nom_client = :client";
        $params[':client'] = $client;
    }

    if ($status === 'paid') {
        $query .= " AND montant_reste_a_payer = 0";
    } elseif ($status === 'unpaid') {
        $query .= " AND montant_reste_a_payer > 0";
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function export_to_csv($data, $filename) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $filename);

    $output = fopen('php://output', 'w');
    fputcsv($output, array_keys($data[0]));

    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
}

function export_to_txt($data, $filename) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment;filename=' . $filename);

    $output = fopen('php://output', 'w');

    $headers = implode("\t", array_keys($data[0]));
    fwrite($output, $headers . PHP_EOL);

    foreach ($data as $row) {
        $line = implode("\t", array_values($row));
        fwrite($output, $line . PHP_EOL);
    }

    fclose($output);
}

function export_to_xml($data, $filename) {
    header('Content-Type: text/xml');
    header('Content-Disposition: attachment;filename=' . $filename);

    $xml = new SimpleXMLElement('<factures/>');

    foreach ($data as $row) {
        $facture = $xml->addChild('facture');
        foreach ($row as $key => $value) {
            $facture->addChild($key, $value);
        }
    }

    echo $xml->asXML();
}

$client = $_POST['client'] ?? '';
$status = $_POST['status'] ?? 'all';
$format = $_POST['format'] ?? 'csv';

$data = fetch_factures($client, $status);

if (!$data) {
    header('HTTP/1.1 404 Not Found');
    echo "No data found for the given filters.";
    exit;
}

$filter = $status === 'all' ? 'All' : ucfirst($status);
$clientName = $client ? $client : 'Global';
$filename = "Factures_{$filter}_{$clientName}.{$format}";

switch ($format) {
    case 'csv':
        export_to_csv($data, $filename);
        break;
    case 'txt':
        export_to_txt($data, $filename);
        break;
    case 'xml':
        export_to_xml($data, $filename);
        break;
    default:
        header('HTTP/1.1 400 Bad Request');
        echo "Invalid format requested.";
        exit;
}
?>
