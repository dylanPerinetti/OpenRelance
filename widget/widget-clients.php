<?php 
    $user_initiales = $_SESSION['user_initiales'];
    $user_id = $_SESSION['user_id'];
?>
<div class="widget-content">
    <h1>Liste des Clients</h1>
    <table class="clients-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Numéro Parma</th>
                <th>Ajouté par</th>
            </tr>
        </thead>
        <tbody id="clients-tbody">
            <tr>
                <td colspan="4">Chargement des données...</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td><i class="fas fa-pencil-alt"></i></td>
                <td><input type="text" id="new-client-name" placeholder="Nom" class="inline-input"></td>
                <td><input type="text" id="new-client-parma" placeholder="Numéro Parma" class="inline-input"></td>
                <td><input type="text" id="new-client-user" value="<?php echo $user_initiales; ?>" class="inline-input" readonly></td>
            </tr>
        </tfoot>
    </table>

    <!-- Pop-up Modal for Confirmation -->
    <div id="confirmation-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Le numéro de Parma est déjà utilisé. Êtes-vous sûr de vouloir ajouter ce client ?</p>
            <button id="confirm-add-btn">Oui</button>
            <button id="cancel-add-btn">Non</button>
        </div>
    </div>

    <script>
        var userInitiales = "<?php echo $user_initiales; ?>";
        var userId = "<?php echo $user_id; ?>";
    </script>
    <script src="scripts/script-clients.js"></script>
</div>