<?php
include '../connexion/mysql-db-config.php';

$conn = get_db_connection('read');
$data = [];

try {
    // Graph 1: Top 5 clients with most unpaid invoices by number of invoices
    $stmt = $conn->query("
        SELECT cl.numeros_parma, cl.nom_client, COUNT(f.id) as num_unpaid_invoices
        FROM clients cl
        JOIN factures f ON cl.id = f.id_clients
        WHERE f.montant_reste_a_payer > 0
        GROUP BY cl.id
        ORDER BY num_unpaid_invoices DESC
        LIMIT 5
    ");
    $data['top5_unpaid_invoices'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Graph 2: Top 5 clients by total amount owed
    $stmt = $conn->query("
        SELECT cl.numeros_parma, cl.nom_client, SUM(f.montant_reste_a_payer) as total_amount_owed
        FROM clients cl
        JOIN factures f ON cl.id = f.id_clients
        WHERE f.montant_reste_a_payer > 0
        GROUP BY cl.id
        ORDER BY total_amount_owed DESC
        LIMIT 5
    ");
    $data['top5_amount_owed'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Graph 3: Pie chart of clients with unpaid invoices vs. those with no unpaid invoices
    $stmt = $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM clients WHERE id IN (SELECT DISTINCT id_clients FROM factures WHERE montant_reste_a_payer > 0)) as clients_with_unpaid,
            (SELECT COUNT(*) FROM clients WHERE id NOT IN (SELECT DISTINCT id_clients FROM factures WHERE montant_reste_a_payer > 0)) as clients_no_unpaid
    ");
    $data['unpaid_vs_paid'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Graph 4: Number of scheduled follow-ups per month for the last 10 months + current month + next month
    $stmt = $conn->query("
        SELECT DATE_FORMAT(date_relance, '%Y-%m') as month, COUNT(*) as relances
        FROM relance_client
        WHERE date_relance BETWEEN DATE_SUB(CURDATE(), INTERVAL 10 MONTH) AND DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 MONTH)
        GROUP BY month
        ORDER BY month ASC
    ");
    $relances_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $months = [];
    $relances = [];

    foreach ($relances_data as $row) {
        $months[] = $row['month'];
        $relances[] = $row['relances'];
    }

    $data['monthly_relances'] = [
        'months' => $months,
        'relances' => $relances
    ];

    echo json_encode($data);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
