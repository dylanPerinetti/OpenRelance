<?php
require_once '../connexion/mysql-db-config.php';

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $db = get_db_connection('read');

    $stmt = $db->prepare("
        (SELECT 'facture' AS type, numeros_de_facture AS result, factures.id, clients.nom_client AS extra 
        FROM factures 
        LEFT JOIN clients ON factures.id_clients = clients.id 
        WHERE numeros_de_facture LIKE ? OR clients.nom_client LIKE ? LIMIT 15)
        UNION
        (SELECT 'client' AS type, nom_client AS result, id, numeros_parma AS extra 
        FROM clients 
        WHERE nom_client LIKE ? OR numeros_parma LIKE ? LIMIT 15)
        UNION
        (SELECT 'contact' AS type, nom_contactes_clients AS result, contactes_clients.id, CONCAT(mail_contactes_clients, ', ', telphone_contactes_clients, ', ', clients.nom_client) AS extra 
        FROM contactes_clients 
        LEFT JOIN clients ON contactes_clients.id_clients = clients.id 
        WHERE nom_contactes_clients LIKE ? OR mail_contactes_clients LIKE ? OR telphone_contactes_clients LIKE ? LIMIT 15)
        UNION
        (SELECT 'user' AS type, nom_user_open_relance AS result, id, CONCAT(prenom_user_open_relance, ', ', email_user_open_relance) AS extra 
        FROM user_open_relance 
        WHERE nom_user_open_relance LIKE ? OR prenom_user_open_relance LIKE ? OR email_user_open_relance LIKE ? LIMIT 15)
    ");
    $searchTerm = '%' . $query . '%';
    $stmt->execute([
        $searchTerm, $searchTerm,
        $searchTerm, $searchTerm,
        $searchTerm, $searchTerm, $searchTerm,
        $searchTerm, $searchTerm, $searchTerm
    ]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        foreach ($results as $row) {
            echo '<div class="search-result" data-id="'.$row['id'].'" data-type="'.$row['type'].'">'.$row['result'].'<br><small>'.$row['extra'].'</small></div>';
        }
    } else {
        echo '<div class="search-result">Aucun résultat trouvé</div>';
    }
}
?>
