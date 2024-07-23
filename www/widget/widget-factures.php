<?php $user_initiales = $_SESSION['user_initiales'];?>
<h1>Liste des Factures</h1>
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
        </tr>
    </thead>
    <tbody id="factures-tbody">
        <tr>
            <td colspan="7">Chargement des données...</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td><i class="fas fa-pencil-alt"></i></td>
            <td><input type="text" id="new-facture-number" placeholder="Numéro de Facture" class="inline-input"></td>
            <td><input type="date" id="new-facture-date" class="inline-input"></td>
            <td><input type="text" id="new-facture-amount" placeholder="Montant" class="inline-input"></td>
            <td><input type="text" id="new-facture-remaining" placeholder="Montant Restant" class="inline-input"></td>
            <td><input type="text" id="new-facture-client" placeholder="Client" class="inline-input"></td>
            <td><input type="text" id="new-facture-user" value="<?php echo $user_initiales; ?>" class="inline-input" readonly></td>
        </tr>
    </tfoot>
</table>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('action/get-factures.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('factures-tbody');
            tbody.innerHTML = ''; // Clear the loading message
            if (data.length > 0) {
                data.forEach(facture => {
                    fetch('action/get-user-initial.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id_user: facture.id_user_open_relance })
                    })
                    .then(response => response.json())
                    .then(userData => {
                        const statusColor = facture.montant_reste_a_payer == 0 ? 'green' : 'orange';
                        const row = document.createElement('tr');
                        row.innerHTML = `
                        <td><span style="color: ${statusColor};">&#9679;</span></td>
                        <td>${facture.numeros_de_facture}</td>
                        <td>${facture.date_echeance_payment}</td>
                        <td>${facture.montant_facture}</td>
                        <td>${facture.montant_reste_a_payer}</td>
                        <td>${facture.nom_client}</td>
                        <td>${userData.initial_user_open_relance}</td>
                        `;
                        tbody.appendChild(row);
                    });
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="7">Aucune facture trouvée</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            const tbody = document.getElementById('factures-tbody');
            tbody.innerHTML = '<tr><td colspan="7">Erreur lors de la récupération des données</td></tr>';
        });

        function autocompleteClient() {
            $("#new-facture-client").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: 'action/search-clients.php',
                        type: 'POST',
                        dataType: 'json',
                        data: JSON.stringify({ search: request.term }),
                        contentType: 'application/json',
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    label: item.nom_client,
                                    value: item.nom_client,
                                    id: item.id
                                };
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    $('#new-facture-client').data('selected-id', ui.item.id);
                }
            });
        }

        autocompleteClient();

        document.getElementById('new-facture-amount').addEventListener('input', function(event) {
            this.value = this.value.replace(/[^\d.]/g, '');
        });

        document.getElementById('new-facture-remaining').addEventListener('input', function(event) {
            this.value = this.value.replace(/[^\d.]/g, '');
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                const factureNumber = document.getElementById('new-facture-number').value;
                const factureDate = document.getElementById('new-facture-date').value;
                const factureAmount = document.getElementById('new-facture-amount').value;
                const factureRemaining = document.getElementById('new-facture-remaining').value;
                const factureClient = $('#new-facture-client').data('selected-id');

                if (factureNumber && factureDate && factureAmount && factureRemaining && factureClient) {
                    addFacture(factureNumber, factureDate, factureAmount, factureRemaining, factureClient);
                } else {
                    alert('Veuillez remplir tous les champs.');
                }
            }
        });

        function addFacture(factureNumber, factureDate, factureAmount, factureRemaining, factureClient) {
            fetch('action/add-facture.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    numeros_de_facture: factureNumber,
                    date_echeance_payment: factureDate,
                    montant_facture: factureAmount,
                    montant_reste_a_payer: factureRemaining,
                    id_clients: factureClient
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetch('action/get-user-initial.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id_user: <?php echo $_SESSION['user_id']; ?> })
                    })
                    .then(response => response.json())
                    .then(userData => {
                        const tbody = document.getElementById('factures-tbody');
                        const statusColor = factureRemaining == 0 ? 'green' : 'orange';
                        const row = document.createElement('tr');
                        row.innerHTML = `
                        <td><span style="color: ${statusColor};">&#9679;</span></td>
                        <td>${factureNumber}</td>
                        <td>${factureDate}</td>
                        <td>${factureAmount}</td>
                        <td>${factureRemaining}</td>
                        <td>${factureClient}</td>
                        <td>${userData.initial_user_open_relance}</td>
                        `;
                        tbody.appendChild(row);
                        document.getElementById('new-facture-number').value = '';
                        document.getElementById('new-facture-date').value = '';
                        document.getElementById('new-facture-amount').value = '';
                        document.getElementById('new-facture-remaining').value = '';
                        document.getElementById('new-facture-client').value = '';
                    });
                } else {
                    alert('Erreur lors de l\'ajout de la facture.');
                }
            });
        }
    });
</script>
