<?php 
$user_initiales = $_SESSION['user_initiales'];
$user_id = $_SESSION['user_id'];
?>
<div class="widget-content">
    <h1>Liste des Factures</h1>
    
    <!-- Filtres et champ de recherche -->
    <div class="filters">
        <label for="search">Recherche:</label>
        <input type="text" id="search" placeholder="Rechercher des factures..." class="form-input">
        <label for="status-filter">Statut:</label>
        <select id="status-filter" class="form-input">
            <option value="tous">Tous</option>
            <option value="non-paye">Afficher les factures non payées uniquement</option>
            <option value="paye">Payé</option>
        </select>
        <!-- Ajout du filtre par date d'échéance -->
        <label for="date-echeance-filter">Date d'échéance avant :</label>
        <input type="date" id="date-echeance-filter" class="form-input" value="<?php echo date('Y-m-d', strtotime('+10 days')); ?>">

        <label for="items-per-page">Factures par page:</label>
        <select id="items-per-page" class="form-input">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50" selected>50</option>
            <option value="100">100</option>
        </select>
    </div>
    <button id="select-toggle-btn" class="form-button">Sélectionner</button>
    <button id="add-comment-btn" class="form-button">Ajouter un commentaire</button>
    <button id="add-relance-btn" class="form-button">Ajouter une relance</button>
    <button id="mark-as-paid-btn" class="form-button">Marquer comme payé</button>
    
    <!-- Pagination haut -->
    <div class="pagination" id="pagination-top"></div>
    
    <table class="factures-table">
        <thead>
            <tr>
                <th style="display: none;"><input type="checkbox" id="select-all" class="hidden-checkbox" style="display: none;"></th>
                <th data-column="statut"><i class="fas fa-info-circle"></i> Statut</th>
                <th data-column="numeros_parma"><i class="fas fa-hashtag"></i> Numéros de Parma</th>
                <th data-column="nom_client"><i class="fas fa-building"></i> Client</th>
                <th data-column="numeros_de_facture"><i class="fas fa-file-invoice"></i> Numéro de Facture</th>
                <th data-column="date_emission_facture"><i class="fas fa-calendar-alt"></i> Date d'Émission</th> 
                <th data-column="date_echeance_payment"><i class="fas fa-calendar-alt"></i> Date d'Échéance</th>
                <th data-column="montant_facture"><i class="fas fa-euro-sign"></i> Montant</th>
            </tr>
        </thead>
        <tbody id="factures-tbody">
            <tr>
                <td colspan="9">Chargement des données...</td>
            </tr>
        </tbody>
    </table>
    <!-- Pagination bas -->
    <div class="pagination" id="pagination-bottom"></div>
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
        <select id="relance-type" class="form-input" required>
            <option value="Reglement à recevoir">Consulter les Règlements</option>
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
<script src="scripts/script-factures.js"></script>