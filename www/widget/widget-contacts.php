<?php 
    $user_initiales = $_SESSION['user_initiales'];
    $user_id = $_SESSION['user_id'];
?>
<div class="widget-content">
    <h1>Liste des Contacts</h1>
    <div class="button-container">
        <button id="import-contacts-btn" class="form-button">Importer Contacts CSV</button>
        <button id="export-contacts-btn" class="form-button">Exporter Contacts CSV</button>
        <input type="file" id="contacts-csv-file" accept=".csv" style="display:none;">
    </div>
    <table class="contacts-table">
        <thead>
            <tr>
                <th><i class="fas fa-hashtag"></i> Numéro Parma</th>
                <th><i class="fas fa-building"></i> Client</th>
                <th><i class="fas fa-user-plus"></i> Nom</th>
                <th><i class="fas fa-briefcase"></i> Fonction</th>
                <th><i class="fas fa-phone"></i> Téléphone</th>
                <th><i class="fas fa-envelope"></i> Email</th>
                <th><i class="fas fa-user"></i> Ajouté Par</th>
            </tr>
            <tr>
                <td><input type="text" id="new-contact-parma" placeholder="Numéro Parma" class="inline-input"></td>
                <td>
                    <input type="text" id="new-contact-client" placeholder="Client" class="inline-input" list="client-suggestions">
                    <datalist id="client-suggestions"></datalist>
                </td>
                <td><input type="text" id="new-contact-name" placeholder="Nom" class="inline-input" value="NaN"></td>
                <td>
                    <select id="new-contact-function" class="inline-input">
                        <option value="">Sélectionner</option>
                        <option value="Service Comptable">Service Comptable</option>
                        <option value="Comptable">Comptable</option>
                        <option value="Gérant">Gérant</option>
                        <option value="Gérant">Autre</option>
                    </select>
                </td>
                <td><input type="text" id="new-contact-phone" placeholder="Téléphone" class="inline-input"></td>
                <td><input type="text" id="new-contact-email" placeholder="Email" class="inline-input"></td>
                <td><button id="add-contact-btn" class="form-button">Ajouter</button></td>
            </tr>
        </thead>
        <tbody id="contacts-tbody">
            <tr>
                <td colspan="7">Chargement des données...</td>
            </tr>
        </tbody>
    </table>

    <script>
        var userInitiales = "<?php echo $user_initiales; ?>";
        var userId = "<?php echo $user_id; ?>";
    </script>
    <script src="scripts/script-contacts.js"></script>
</div>

<!-- Modal pour afficher et modifier les données d'un contact -->
<div id="contact-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Détails du Contact</h2>
        <form id="contact-form">
            <input type="hidden" id="contact-id">
            <div class="form-group">
                <label for="contact-fonction">Fonction</label>
                <input type="text" id="contact-fonction" class="form-input" readonly>
            </div>
            <div class="form-group">
                <label for="contact-nom">Nom</label>
                <input type="text" id="contact-nom" class="form-input" readonly>
            </div>
            <div class="form-group">
                <label for="contact-email">Email</label>
                <input type="text" id="contact-email" class="form-input" readonly>
            </div>
            <div class="form-group">
                <label for="contact-telephone">Téléphone</label>
                <input type="text" id="contact-telephone" class="form-input" readonly>
            </div>
            <div class="form-group">
                <label for="contact-client">Client</label>
                <input type="text" id="contact-client" class="form-input" readonly>
            </div>
            <div class="form-group">
                <label for="contact-parma">Numéro Parma</label>
                <input type="text" id="contact-parma" class="form-input" readonly>
            </div>
            <div class="form-group buttons-group">
                <button type="button" id="edit-contact-btn" class="form-button"><i class="fas fa-pencil-alt"></i> Modifier</button>
                <button type="button" id="save-contact-btn" class="form-button" style="display:none;">Enregistrer</button>
            </div>
        </form>
    </div>
</div>