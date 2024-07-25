<?php
require_once '../connexion/mysql-db-config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $search = $data['search'];
    
    try {
        $pdo = get_db_connection('read');
        $stmt = $pdo->prepare('
            SELECT id, nom_contactes_clients 
            FROM contactes_clients 
            WHERE nom_contactes_clients LIKE :search
        ');
        $stmt->execute(['search' => "%$search%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
    } catch (PDOException $e) {
        log_error('Erreur lors de la recherche de contacts : ' . $e->getMessage());
        echo json_encode([]);
    }
}
?>
