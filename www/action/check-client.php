<?php
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');
$data = json_decode(file_get_contents('php://input'), true);

$numeros_parma = $data['numeros_parma'];
$nom_client = $data['nom_client'];

// Vérifier le numéro de Parma
$sqlParma = "SELECT COUNT(*) FROM clients WHERE numeros_parma = :numeros_parma";
$stmtParma = $conn->prepare($sqlParma);
$stmtParma->bindParam(':numeros_parma', $numeros_parma);
$stmtParma->execute();
$existsParma = $stmtParma->fetchColumn() > 0;

// Vérifier le nom du client
$sqlNom = "SELECT COUNT(*) FROM clients WHERE nom_client = :nom_client";
$stmtNom = $conn->prepare($sqlNom);
$stmtNom->bindParam(':nom_client', $nom_client);
$stmtNom->execute();
$existsNom = $stmtNom->fetchColumn() > 0;

$response = ['exists' => $existsParma, 'nameExists' => $existsNom];

echo json_encode($response);
?>
