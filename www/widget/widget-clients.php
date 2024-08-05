<?php
$user_initiales = htmlspecialchars($_SESSION['user_initiales'], ENT_QUOTES, 'UTF-8');
$user_id = htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8');
?>
<div class="widget-content">
    <div class="button-container">
        <button id="import-csv-btn" class="form-button">Importer CSV</button>
        <button id="export-csv-btn" class="form-button">Exporter CSV</button>
        <input type="file" id="csv-file-input" style="display: none;">
        <div id="total-impayes" style="font-weight: bold;padding-left:80px; font-size: 20px; text-align: center;display: inline-block;">
            Total général des impayés: <span style="font-size: 25px;" id="total-impayes-value">0€</span>
        </div>
    </div>
    <table class="clients-table">
        <thead>
            <tr>
                <th data-column="numeros_parma"><i class="fas fa-hashtag"></i> Numéro Parma</th>
                <th data-column="nom_client"><i class="fas fa-building"></i> Nom</th>
                <th data-column="nb_factures_non_payees" style="text-align: center;"><i class="fas fa-file-invoice"></i> Nb Factures Non Payées</th>
                <th data-column="montant_du" style="text-align: right;"><i class="fas fa-euro-sign"></i> Montant dû</th>
            </tr>
            <tr>
                <td><input type="text" id="new-client-parma" placeholder="Numéro Parma" class="inline-input"></td>
                <td><input type="text" id="new-client-name" placeholder="Nom" class="inline-input"></td>
                <td colspan="3"><button id="add-client-btn" class="form-button">Enregistrer</button></td>
            </tr>
        </thead>
        <tbody id="clients-tbody">
            <tr>
                <td colspan="4">Chargement des données...</td>
            </tr>
        </tbody>
    </table>
    <script src="scripts/script-clients.js"></script>
</div>

<!-- Pop-up Modal for Confirmation -->
<div id="confirmation-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Le numéro de Parma est déjà utilisé. Êtes-vous sûr de vouloir ajouter ce client ?</p>
        <div class="button-container">
            <button id="cancel-add-btn" class="cancelbtn">Non</button>
            <button id="confirm-add-btn" class="signupbtn">Oui</button>
        </div>
    </div>
</div>

<!-- Pop-up Modal for CSV Preview -->
<div id="csv-preview-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Prévisualiser les données à importer</p>
        <table id="csv-preview-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Numéro Parma</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="button-container">
            <button id="cancel-import-btn" class="cancelbtn">Annuler</button>
            <button id="confirm-import-btn" class="signupbtn">Importer</button>
        </div>
    </div>
</div>

<style>
    .client-exists {
        background-color: #ffcccc; /* Rouge pâle */
    }

    .parma-exists {
        background-color: #ffebcc; /* Orange pâle */
    }

    .clients-table th {
        cursor: pointer;
    }

    .clients-table td.montant-du {
        text-align: right;
    }
</style>
