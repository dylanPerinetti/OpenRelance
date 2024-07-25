<?php 
    $user_initiales = $_SESSION['user_initiales'];
    $user_id = $_SESSION['user_id'];
?>
<div class="widget-content">
    <h1>Liste des Factures</h1>

    <!-- Formulaire d'ajout de facture -->
    <div id="add-facture-form">
        <input type="text" id="new-facture-number" placeholder="Numéro de Facture" class="form-input">
        <input type="date" id="new-facture-date" class="form-input">
        <input type="text" id="new-facture-amount" placeholder="Montant" class="form-input">
        <input type="text" id="new-facture-remaining" placeholder="Montant Restant" class="form-input">
        <input type="text" id="new-facture-client" placeholder="Client" class="form-input">
        <input type="text" id="new-facture-user" value="<?php echo $user_initiales; ?>" class="form-input" readonly>
        <button id="add-facture-button" class="form-button">Ajouter Facture</button>
    </div>

    <table class="factures-table">
        <thead>
            <tr>
                <th>Status</th>
                <th>Numéro de Facture</th>
                <th>Date d'Échéance</th>
                <th>Montant</th>
                <th>Montant Restant</th>
                <th>Client</th>
                <th>Ajouté par</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="factures-tbody">
            <tr>
                <td colspan="8">Chargement des données...</td>
            </tr>
        </tbody>
    </table>

    <script>
        var userInitiales = "<?php echo $user_initiales; ?>";
        var userId = "<?php echo $user_id; ?>";

        // Fonction pour ajouter une facture
        document.getElementById('add-facture-button').addEventListener('click', function() {
            var data = {
                numeros_de_facture: document.getElementById('new-facture-number').value,
                date_echeance_payment: document.getElementById('new-facture-date').value,
                montant_facture: document.getElementById('new-facture-amount').value,
                montant_reste_a_payer: document.getElementById('new-facture-remaining').value,
                id_clients: document.getElementById('new-facture-client').value,
                id_user_open_relance: userId
            };

            fetch('add-facture.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Facture ajoutée avec succès!');
                    location.reload();
                } else {
                    alert('Erreur lors de l\'ajout de la facture.');
                }
            })
            .catch(error => console.error('Erreur:', error));
        });

        // Fonction pour activer l'édition sur une ligne
        function enableEditing(row) {
            row.querySelectorAll('td[data-editable]').forEach(td => {
                const input = document.createElement('input');
                input.type = 'text';
                input.value = td.innerText;
                input.classList.add('editable-input');
                td.innerHTML = '';
                td.appendChild(input);
            });
            const editIcon = row.querySelector('.edit-icon');
            editIcon.classList.remove('fa-pencil-alt');
            editIcon.classList.add('fa-check');
            editIcon.classList.add('validate-icon');
        }

        // Fonction pour désactiver l'édition et enregistrer les modifications
        function disableEditing(row) {
            row.querySelectorAll('td[data-editable]').forEach(td => {
                const input = td.querySelector('input');
                td.innerText = input.value;
            });
            const validateIcon = row.querySelector('.validate-icon');
            validateIcon.classList.remove('fa-check');
            validateIcon.classList.add('fa-pencil-alt');
            validateIcon.classList.remove('validate-icon');
            updateFacture(row);
        }

        // Fonction pour mettre à jour une facture
        function updateFacture(row) {
            const data = {
                id: row.dataset.id,
                status: row.querySelector('td[data-field="status"]').innerText,
                numeros_de_facture: row.querySelector('td[data-field="numeros_de_facture"]').innerText,
                date_echeance_payment: row.querySelector('td[data-field="date_echeance_payment"]').innerText,
                montant_facture: row.querySelector('td[data-field="montant_facture"]').innerText,
                montant_reste_a_payer: row.querySelector('td[data-field="montant_reste_a_payer"]').innerText,
                id_clients: row.querySelector('td[data-field="id_clients"]').innerText,
                id_user_open_relance: userId
            };

            fetch('update-facture.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Facture mise à jour avec succès!');
                    row.querySelector('td[data-field="id_user_open_relance"]').innerText = userInitiales;
                } else {
                    alert('Erreur lors de la mise à jour de la facture.');
                }
            })
            .catch(error => console.error('Erreur:', error));
        }

        // Ajout des icônes de crayon et gestion de l'édition
        document.getElementById('factures-tbody').addEventListener('click', function(event) {
            const row = event.target.closest('tr');
            const editIcon = row.querySelector('.edit-icon');
            if (editIcon) {
                if (editIcon.classList.contains('fa-pencil-alt')) {
                    enableEditing(row);
                } else if (editIcon.classList.contains('fa-check')) {
                    disableEditing(row);
                }
            }
        });
        // Charger les factures et ajouter les icônes de crayon
        function loadFactures() {
            fetch('action/get-factures.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('factures-tbody');
                    tbody.innerHTML = '';
                    data.forEach(facture => {
                        const tr = document.createElement('tr');
                        tr.dataset.id = facture.id;

                        const tdStatus = document.createElement('td');
                        tdStatus.innerText = facture.status;
                        tdStatus.dataset.field = 'status';
                        tdStatus.dataset.editable = true;

                        const tdNumber = document.createElement('td');
                        tdNumber.innerText = facture.numeros_de_facture;
                        tdNumber.dataset.field = 'numeros_de_facture';
                        tdNumber.dataset.editable = true;

                        const tdDate = document.createElement('td');
                        tdDate.innerText = facture.date_echeance_payment;
                        tdDate.dataset.field = 'date_echeance_payment';
                        tdDate.dataset.editable = true;

                        const tdAmount = document.createElement('td');
                        tdAmount.innerText = facture.montant_facture;
                        tdAmount.dataset.field = 'montant_facture';
                        tdAmount.dataset.editable = true;

                        const tdRemaining = document.createElement('td');
                        tdRemaining.innerText = facture.montant_reste_a_payer;
                        tdRemaining.dataset.field = 'montant_reste_a_payer';
                        tdRemaining.dataset.editable = true;

                        const tdClient = document.createElement('td');
                        tdClient.innerText = facture.id_clients;
                        tdClient.dataset.field = 'id_clients';
                        tdClient.dataset.editable = true;

                        const tdUser = document.createElement('td');
                        tdUser.innerText = facture.id_user_open_relance;

                        const tdAction = document.createElement('td');
                        const editIcon = document.createElement('i');
                        editIcon.className = 'fas fa-pencil-alt edit-icon';
                        tdAction.appendChild(editIcon);

                        tr.appendChild(tdStatus);
                        tr.appendChild(tdNumber);
                        tr.appendChild(tdDate);
                        tr.appendChild(tdAmount);
                        tr.appendChild(tdRemaining);
                        tr.appendChild(tdClient);
                        tr.appendChild(tdUser);
                        tr.appendChild(tdAction);

                        tbody.appendChild(tr);
                    });
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Charger les factures au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadFactures();
        });
    </script>

    <script src="scripts/script-factures.js"></script>
</div>

<style>
    #add-facture-form {
        margin-bottom: 20px;
    }
    .form-input {
        margin-right: 10px;
    }
    .form-button {
        padding: 5px 10px;
    }
    .edit-icon {
        cursor: pointer;
    }
    .validate-icon {
        color: green;
    }
    .editable-input {
        width: 100%;
        box-sizing: border-box;
    }
</style>
