<?php
require_once '../connexion/mysql-db-config.php';

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $db = get_db_connection('read');

    // Recherche dans les factures
    $stmtFactures = $db->prepare("
        SELECT numeros_de_facture, clients.nom_client 
        FROM factures 
        LEFT JOIN clients ON factures.id_clients = clients.id 
        WHERE numeros_de_facture LIKE ? OR clients.nom_client LIKE ? 
        LIMIT 15
    ");
    $stmtFactures->execute(['%' . $query . '%', '%' . $query . '%']);
    $factures = $stmtFactures->fetchAll(PDO::FETCH_ASSOC);

    // Recherche dans les clients
    $stmtClients = $db->prepare("
        SELECT nom_client, numeros_parma 
        FROM clients 
        WHERE nom_client LIKE ? OR numeros_parma LIKE ? 
        LIMIT 15
    ");
    $stmtClients->execute(['%' . $query . '%', '%' . $query . '%']);
    $clients = $stmtClients->fetchAll(PDO::FETCH_ASSOC);

    // Recherche dans les contacts des clients
    $stmtContacts = $db->prepare("
        SELECT nom_contactes_clients, mail_contactes_clients, telphone_contactes_clients, clients.nom_client 
        FROM contactes_clients 
        LEFT JOIN clients ON contactes_clients.id_clients = clients.id 
        WHERE nom_contactes_clients LIKE ? OR mail_contactes_clients LIKE ? OR telphone_contactes_clients LIKE ? 
        LIMIT 15
    ");
    $stmtContacts->execute(['%' . $query . '%', '%' . $query . '%', '%' . $query . '%']);
    $contacts = $stmtContacts->fetchAll(PDO::FETCH_ASSOC);

    // Recherche dans les utilisateurs OpenRelance
    $stmtUsers = $db->prepare("
        SELECT nom_user_open_relance, prenom_user_open_relance, email_user_open_relance 
        FROM user_open_relance 
        WHERE nom_user_open_relance LIKE ? OR prenom_user_open_relance LIKE ? OR email_user_open_relance LIKE ? 
        LIMIT 15
    ");
    $stmtUsers->execute(['%' . $query . '%', '%' . $query . '%', '%' . $query . '%']);
    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

    // Affichage des résultats
    echo '<h2>Factures</h2>';
    if ($factures) {
        echo '<table>';
        echo '<tr><th>Numéro</th><th>Client</th></tr>';
        foreach ($factures as $facture) {
            echo '<tr><td>' . $facture['numeros_de_facture'] . '</td><td>' . $facture['nom_client'] . '</td></tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Aucune facture trouvée.</p>';
    }

    echo '<h2>Clients</h2>';
    if ($clients) {
        echo '<table>';
        echo '<tr><th>Nom</th><th>Numéro Parma</th></tr>';
        foreach ($clients as $client) {
            echo '<tr><td>' . $client['nom_client'] . '</td><td>' . $client['numeros_parma'] . '</td></tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Aucun client trouvé.</p>';
    }

    echo '<h2>Contacts</h2>';
    if ($contacts) {
        echo '<table>';
        echo '<tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Client</th></tr>';
        foreach ($contacts as $contact) {
            echo '<tr><td>' . $contact['nom_contactes_clients'] . '</td><td>' . $contact['mail_contactes_clients'] . '</td><td>' . $contact['telphone_contactes_clients'] . '</td><td>' . $contact['nom_client'] . '</td></tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Aucun contact trouvé.</p>';
    }

    echo '<h2>Utilisateurs OpenRelance</h2>';
    if ($users) {
        echo '<table>';
        echo '<tr><th>Nom</th><th>Prénom</th><th>Email</th></tr>';
        foreach ($users as $user) {
            echo '<tr><td>' . $user['nom_user_open_relance'] . '</td><td>' . $user['prenom_user_open_relance'] . '</td><td>' . $user['email_user_open_relance'] . '</td></tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Aucun utilisateur trouvé.</p>';
    }
}
?>
