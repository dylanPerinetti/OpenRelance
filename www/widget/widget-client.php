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
    <button id="select-toggle-btn" class="form-button">Sélectionner</button>
    <button id="add-comment-btn" class="form-button">Ajouter un commentaire</button>
    <button id="add-relance-btn" class="form-button">Ajouter une relance</button>
    <button id="mark-as-paid-btn" class="form-button">Marquer comme payé</button>
    <h3>Factures Impayées</h3>
    <table class="factures-table">
        <thead>
            <tr>
                <th style="text-align:center;"><input type="checkbox" id="select-all" class="hidden-checkbox" style="display:none;"></th>
                <th style="text-align:center;">Status</th>
                <th><i class="fas fa-file-invoice"></i> Numéro de Facture</th>
                <th><i class="fas fa-calendar-alt"></i> Date d'émission</th>
                <th><i class="fas fa-calendar-alt"></i> Date d'Échéance</th>
                <th style="text-align: right;"> Montant de la facture</th>
                <th style="text-align: right;"> Montant à Payer Restant</th>
            </tr>
        </thead>
        <tbody id="factures-impayees-tbody">
            <tr>
                <td colspan="7">Chargement des factures...</td>
            </tr>
        </tbody>
    </table>
    <h3>Factures Payées</h3>
    <table class="factures-table">
        <thead>
            <tr>
                <th style="text-align:center;"><input type="checkbox" id="select-all-paid" class="hidden-checkbox" style="display:none;"></th>
                <th style="text-align:center;">Status</th>
                <th><i class="fas fa-file-invoice"></i> Numéro de Facture</th>
                <th><i class="fas fa-calendar-alt"></i> Date d'émission</th>
                <th><i class="fas fa-calendar-alt"></i> Date d'Échéance</th>
                <th style="text-align: right;"> Montant de la facture</th>
                <th style="text-align: right;"> Montant à Payer Restant</th>
            </tr>
        </thead>
        <tbody id="factures-payees-tbody">
            <tr>
                <td colspan="7">Chargement des factures...</td>
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
<!-- Modal pour ajouter des commentaires -->
<div id="comment-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Ajouter un commentaire</h2>
        <textarea id="comment-text" rows="4" cols="50" placeholder="Ajouter votre commentaire ici..." class="form-input"></textarea>
        <button id="save-comment-btn" class="form-button">Enregistrer</button>
    </div>
</div>
<!-- Modal pour ajouter des relances -->
<div id="relance-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Ajouter une relance</h2>
        <label for="relance-type">Type de relance:</label>
        <select id="relance-type" class="form-input">
            <option value="Reglement à recevoir">Consulter les Règlement</option>
            <option value="Appel">Appel</option>
            <option value="mail">Mail</option>
            <option value="courier 1">Courier 1</option>
            <option value="courier 2">Courier 2</option>
            <option value="recommandé">Recommandé</option>
            <option value="litige">Litige</option>
        </select>
        <label for="contact-client">Contact Client (optionnel):</label>
        <select id="contact-client" class="form-input">
            <option value="">Sélectionner un contact (optionnel)</option>
        </select>
        <label for="relance-date">Date de relance:</label>
        <input type="date" id="relance-date" class="form-input" required>
        <label for="relance-comment">Commentaire:</label>
        <textarea id="relance-comment" rows="4" cols="50" placeholder="Ajouter votre commentaire ici..." class="form-input"></textarea>
        <button id="save-relance-btn" class="form-button">Enregistrer</button>
    </div>
</div>

<script>
    var userInitiales = "<?php echo $user_initiales; ?>";
    var userId = "<?php echo $user_id; ?>";
</script>

<style>
    .status-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .select-facture {
        cursor: pointer;
        display: none;
    }

    .hidden-checkbox {
        display: none;
    }
</style>
