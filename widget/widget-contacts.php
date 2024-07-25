<?php 
    $user_initiales = $_SESSION['user_initiales'];
    $user_id = $_SESSION['user_id'];
?>
<div class="widget-content">
    <h1>Liste des Contacts</h1>
    <table class="contacts-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fonction</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Client Associé</th>
            </tr>
        </thead>
        <tbody id="contacts-tbody">
            <tr>
                <td colspan="6">Chargement des données...</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td><i class="fas fa-pencil-alt"></i></td>
                <td><input type="text" id="new-contact-function" placeholder="Fonction" class="inline-input"></td>
                <td><input type="text" id="new-contact-name" placeholder="Nom" class="inline-input"></td>
                <td><input type="text" id="new-contact-email" placeholder="Email" class="inline-input"></td>
                <td><input type="text" id="new-contact-phone" placeholder="Téléphone" class="inline-input"></td>
                <td><input type="text" id="new-contact-client" placeholder="Client" class="inline-input"></td>
            </tr>
        </tfoot>
    </table>

    <script>
        var userInitiales = "<?php echo $user_initiales; ?>";
        var userId = "<?php echo $user_id; ?>";
    </script>
    <script src="scripts/script-contacts.js"></script>
</div>