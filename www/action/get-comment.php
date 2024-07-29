<?php
// action/get-comment.php
include '../connexion/mysql-db-config.php'; // Chemin mis à jour pour inclure le fichier de configuration

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $factureId = isset($_GET['factureId']) ? intval($_GET['factureId']) : 0;

    if ($factureId > 0) {
        $pdo = get_db_connection('read');
        
        $query = $pdo->prepare('SELECT c.id, c.date_commentaire, c.message_commentaire, u.initial_user_open_relance 
                                FROM commentaires c 
                                JOIN user_open_relance u ON c.id_user_open_relance = u.id 
                                JOIN commentaires_factures cf ON c.id = cf.id_commentaire 
                                WHERE cf.id_facture = ?');
        $query->execute([$factureId]);
        $commentaires = $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'commentaires' => $commentaires]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid facture ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
