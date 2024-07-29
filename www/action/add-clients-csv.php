<?php
session_start();
include '../connexion/mysql-db-config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($file, 'r');
    $conn = get_db_connection('add');
    $readConn = get_db_connection('read');
    $response = ['success' => false, 'warnings' => [], 'errors' => []];

    if ($handle) {
        $isFirstRow = true;
        $parmaIndex = -1;
        $nameIndex = -1;
        
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            if ($isFirstRow) {
                $isFirstRow = false; // Skip the header row
                $header = array_map('trim', $data);
                $parmaIndex = array_search('parma', array_map('strtolower', $header));
                $nameIndex = array_search('client', array_map('strtolower', $header));

                if ($parmaIndex === false) {
                    $parmaIndex = array_search('entreprise', array_map('strtolower', $header));
                }
                if ($nameIndex === false) {
                    $nameIndex = array_search('nom', array_map('strtolower', $header));
                }

                if ($parmaIndex === false || $nameIndex === false) {
                    $response['errors'][] = "Le fichier CSV doit contenir des colonnes avec les mots 'parma' et 'client/entreprise/nom'.";
                    break;
                }
                continue;
            }

            $nom_client = strtoupper(trim($data[$nameIndex]));
            $numeros_parma = trim($data[$parmaIndex]);
            $id_user_open_relance = $_SESSION['user_id'];

            if (empty($nom_client) || empty($numeros_parma)) {
                continue; // Skip empty rows
            }

            if (!is_numeric($numeros_parma)) {
                $response['errors'][] = "Le numéro de Parma '$numeros_parma' n'est pas valide.";
                continue;
            }

            // Check if the client name or parma number already exists
            $stmt = $readConn->prepare("SELECT * FROM clients WHERE nom_client = :nom_client OR numeros_parma = :numeros_parma");
            $stmt->bindParam(':nom_client', $nom_client, PDO::PARAM_STR);
            $stmt->bindParam(':numeros_parma', $numeros_parma, PDO::PARAM_STR);
            $stmt->execute();
            $existingClient = $stmt->fetch();

            if ($existingClient) {
                if ($existingClient['nom_client'] === $nom_client) {
                    $response['errors'][] = "Le client '$nom_client' existe déjà.";
                } else if ($existingClient['numeros_parma'] === $numeros_parma) {
                    $response['errors'][] = "Le numéro de Parma '$numeros_parma' est déjà utilisé.";
                }
            } else {
                $sql = "INSERT INTO clients (nom_client, numeros_parma, id_user_open_relance) VALUES (:nom_client, :numeros_parma, :id_user_open_relance)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':nom_client', $nom_client, PDO::PARAM_STR);
                $stmt->bindParam(':numeros_parma', $numeros_parma, PDO::PARAM_STR);
                $stmt->bindParam(':id_user_open_relance', $id_user_open_relance, PDO::PARAM_INT);

                try {
                    if (!$stmt->execute()) {
                        $response['errors'][] = "Erreur lors de l'ajout du client: $nom_client, $numeros_parma";
                    }
                } catch (PDOException $e) {
                    $response['errors'][] = "Erreur PDO: " . $e->getMessage();
                }
            }
        }
        fclose($handle);
        $response['success'] = empty($response['errors']);
    } else {
        $response['errors'][] = "Erreur lors de la lecture du fichier.";
    }

    echo json_encode($response);
}
?>
