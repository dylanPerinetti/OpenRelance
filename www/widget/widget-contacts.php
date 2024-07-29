<?php 
    $user_initiales = $_SESSION['user_initiales'];
    $user_id = $_SESSION['user_id'];
?>
<div class="widget-content">
    <h1>Liste des Contacts</h1>
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
                <td><input type="text" id="new-contact-name" placeholder="Nom" class="inline-input"></td>
                <td>
                    <select id="new-contact-function" class="inline-input">
                        <option value="">Sélectionner</option>
                        <option value="Comptable">Comptable</option>
                        <option value="Gérant">Gérant</option>
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
