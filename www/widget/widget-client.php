<?php
$user_initiales = $_SESSION['user_initiales'];
$user_id = $_SESSION['user_id'];
?>
<div class="widget-content">
    <h1>Informations sur le Client</h1>
    <div id="client-info">
        <p>Chargement des informations du client...</p>
    </div>
    <h2>Relances</h2>
    <div id="relance-info">
        <p>Chargement des relances...</p>
    </div>
    <h2>Factures</h2>
    <p id="total-due">Total dû à ce jour: <strong>0,00 €</strong></p>
    <p id="unpaid-count">Il y a 0 facture(s) impayée(s) à ce jour.</p>
    <h3>Factures Impayées</h3>
    <table class="factures-table">
        <thead>
            <tr>
                <th><i class="fas fa-file-invoice"></i> Numéro de Facture</th>
                <th><i class="fas fa-calendar-alt"></i> Date d'émission</th>
                <th><i class="fas fa-calendar-alt"></i> Date d'Échéance</th>
                <th style="text-align: right;"> Montant de la facture</th>
                <th style="text-align: right;"> Montant à Payer Restant</th>
            </tr>
        </thead>
        <tbody id="factures-impayees-tbody">
            <tr>
                <td colspan="5">Chargement des factures...</td>
            </tr>
        </tbody>
    </table>
    <h3>Factures Payées</h3>
    <table class="factures-table">
        <thead>
            <tr>
                <th><i class="fas fa-file-invoice"></i> Numéro de Facture</th>
                <th><i class="fas fa-calendar-alt"></i> Date d'émission</th>
                <th><i class="fas fa-calendar-alt"></i> Date d'Échéance</th>
                <th style="text-align: right;"> Montant de la facture</th>
                <th style="text-align: right;"> Montant à Payer Restant</th>
            </tr>
        </thead>
        <tbody id="factures-payees-tbody">
            <tr>
                <td colspan="5">Chargement des factures...</td>
            </tr>
        </tbody>
    </table>
    <h2>Contacts</h2>
    <table class="contacts-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Fonction</th>
                <th><i class="fas fa-phone"></i> Téléphone</th>
                <th><i class="fas fa-envelope"></i> Email</th>
            </tr>
        </thead>
        <tbody id="contacts-tbody">
            <tr>
                <td colspan="4">Chargement des contacts...</td>
            </tr>
        </tbody>
    </table>
    <button id="download-pdf">Télécharger en PDF</button>
    <script>
        var clientId = <?php echo $client_id; ?>;
    </script>
    <script src="scripts/script-client.js"></script>
</div>
