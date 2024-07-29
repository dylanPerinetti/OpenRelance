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
            <option value="non-paye">Afficher les factures non payées uniquement</option>
            <option value="tous">Tous</option>
            <option value="paye">Payé</option>
        </select>
        
        <label for="items-per-page">Factures par page:</label>
        <select id="items-per-page" class="form-input">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
    </div>
    <button id="select-toggle-btn" class="form-button">Sélectionner</button>
    <button id="add-comment-btn" class="form-button">Ajouter un commentaire</button>
    
    <!-- Pagination haut -->
    <div class="pagination" id="pagination-top"></div>
    
    <table class="factures-table">
        <thead>
            <tr>
                <th style="display: none;"><input type="checkbox" id="select-all" class="hidden-checkbox"></th>
                <th><i class="fas fa-info-circle"></i> Statut</th>
                <th><i class="fas fa-hashtag"></i> Numéros de Parma</th>
                <th><i class="fas fa-building"></i> Client</th>
                <th><i class="fas fa-file-invoice"></i> Numéro de Facture</th>
                <th><i class="fas fa-calendar-alt"></i> Date d'Émission</th> 
                <th><i class="fas fa-calendar-alt"></i> Date d'Échéance</th>
                <th><i class="fas fa-euro-sign"></i> Montant</th>
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
    <script>
        var userInitiales = "<?php echo $user_initiales; ?>";
        var userId = "<?php echo $user_id; ?>";
    </script>
    <script src="scripts/script-factures.js"></script>
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
