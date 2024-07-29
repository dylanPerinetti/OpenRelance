<?php 
$user_initiales = htmlspecialchars($_SESSION['user_initiales'], ENT_QUOTES, 'UTF-8');
?>
<div class="widget-content">
    <button id="show-import-balance-form" class="form-button">Importer balance (From SAP)</button>
    
    <form id="import-balance-form" style="display: none;">
        <div class="form-group">
            <label for="balance-csv-file">Sélectionnez un fichier CSV</label>
            <input type="file" id="balance-csv-file" accept=".csv" class="form-input">
        </div>
        <div class="form-group buttons-group">
            <button type="button" id="cancel-import-balance" class="form-button cancel-button">Annuler</button>
            <button type="submit" id="import-balance-button" class="form-button submit-button">Importer</button>
        </div>
    </form>
    <button id="show-add-facture-form" class="form-button">Ajouter une facture</button>
    <form id="add-facture-form" style="display: none;">
        <div class="form-row">
            <div class="form-group">
                <label for="new-facture-number">Numéro de Facture</label>
                <input type="text" id="new-facture-number" placeholder="Numéro de Facture" class="form-input">
            </div>
            <div class="form-group">
                <label for="new-facture-date-emission">Date d'Émission</label>
                <input type="date" id="new-facture-date-emission" class="form-input">
            </div>
            <div class="form-group">
                <label for="new-facture-date">Date d'Échéance</label>
                <input type="date" id="new-facture-date" class="form-input">
            </div>
            <div class="form-group">
                <label for="new-facture-amount">Montant</label>
                <input type="text" id="new-facture-amount" placeholder="Montant" class="form-input">
            </div>
            <div class="form-group">
                <label for="new-facture-paid">Montant Payé</label>
                <input type="text" id="new-facture-paid" value="0" placeholder="Montant Payé" class="form-input">
            </div>
            <div class="form-group">
                <label for="new-facture-client">Client</label>
                <input type="text" id="new-facture-client" placeholder="Client" class="form-input">
            </div>
            <div class="form-group buttons-group">
                <button type="button" id="cancel-add-facture" class="form-button cancel-button">Annuler</button>
                <button type="submit" id="add-facture-button" class="form-button submit-button">Enregistrer</button>
            </div>
        </div>
    </form>
    <button id="show-export-form" class="form-button">Exporter des factures</button>
    <form id="export-facture-form" style="display: none;">
        <div class="form-row">
            <div class="form-group">
                <label for="export-client">Client</label>
                <input type="text" id="export-client" placeholder="Nom du client" class="form-input">
            </div>
            <div class="form-group">
                <label for="export-status">Statut de la facture</label>
                <select id="export-status" class="form-input">
                    <option value="all">Toutes les factures</option>
                    <option value="paid">Factures payées</option>
                    <option value="unpaid">Factures impayées</option>
                </select>
            </div>
            <div class="form-group">
                <label for="export-format">Format</label>
                <select id="export-format" class="form-input">
                    <option value="csv">CSV (.csv)</option>
                    <option value="txt">Texte (.txt)</option>
                    <option value="xml">XML (.xml)</option>
                </select>
            </div>
            <div class="form-group buttons-group">
                <button type="button" id="cancel-export-facture" class="form-button cancel-button">Annuler</button>
                <button type="submit" id="export-facture-button" class="form-button submit-button">Exporter</button>
            </div>
        </div>
    </form>
</div>
<!-- Modal pour prévisualisation -->
<div id="balance-preview-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Prévisualisation des données</p>
        <table id="balance-preview-table">
            <thead>
                <tr>
                    <th>Customer Number</th>
                    <th>Customer Name</th>
                    <th>Document Date</th>
                    <th>Reference</th>
                    <th>Due Date</th>
                    <th>Amount in Local Cur</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="button-container">
            <button id="cancel-import-preview" class="cancelbtn">Annuler</button>
            <button id="confirm-import-preview" class="signupbtn">Importer</button>
        </div>
    </div>
</div>
<script>
    var userInitiales = "<?php echo $user_initiales; ?>";
</script>
<script src="scripts/script-add-facture.js"></script>
<script src="scripts/script-add-balance-csv.js"></script>

