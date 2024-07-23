<?php $user_initiales = $_SESSION['user_initiales']; ?>
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
    document.addEventListener('DOMContentLoaded', function() {
        fetch('action/get-contacts.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('contacts-tbody');
            tbody.innerHTML = ''; // Clear the loading message
            if (data.length > 0) {
                data.forEach(contact => {
                    fetch('action/get-client-name.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id_client: contact.id_clients })
                    })
                    .then(response => response.json())
                    .then(clientData => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                        <td>${contact.id}</td>
                        <td>${contact.fonction_contactes_clients}</td>
                        <td>${contact.nom_contactes_clients}</td>
                        <td>${contact.mail_contactes_clients}</td>
                        <td>${contact.telphone_contactes_clients}</td>
                        <td>${clientData.nom_client}</td>
                        `;
                        tbody.appendChild(row);
                    });
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="6">Aucun contact trouvé</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            const tbody = document.getElementById('contacts-tbody');
            tbody.innerHTML = '<tr><td colspan="6">Erreur lors de la récupération des données</td></tr>';
        });

        function autocompleteClient() {
            $("#new-contact-client").autocomplete({
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
                    $('#new-contact-client').data('selected-id', ui.item.id);
                }
            });
        }

        autocompleteClient();

        document.getElementById('new-contact-phone').addEventListener('input', function(event) {
            this.value = this.value.replace(/\D/g, '');
        });

        document.getElementById('new-contact-name').addEventListener('input', function(event) {
            this.value = this.value.toUpperCase();
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                const functionContact = document.getElementById('new-contact-function').value;
                const name = document.getElementById('new-contact-name').value;
                const email = document.getElementById('new-contact-email').value;
                const phone = document.getElementById('new-contact-phone').value;
                const client = $('#new-contact-client').data('selected-id');

                if (functionContact && name && email && phone && client) {
                    addContact(functionContact, name, email, phone, client);
                } else {
                    alert('Veuillez remplir tous les champs.');
                }
            }
        });

        function addContact(functionContact, name, email, phone, client) {
            fetch('action/add-contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    fonction_contactes_clients: functionContact, 
                    nom_contactes_clients: name, 
                    mail_contactes_clients: email, 
                    telphone_contactes_clients: phone, 
                    id_clients: client 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tbody = document.getElementById('contacts-tbody');
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td>${data.new_id}</td>
                    <td>${functionContact}</td>
                    <td>${name}</td>
                    <td>${email}</td>
                    <td>${phone}</td>
                    <td>${document.querySelector(`#new-contact-client option[value='${client}']`).text}</td>
                    `;
                    tbody.appendChild(row);
                    document.getElementById('new-contact-function').value = '';
                    document.getElementById('new-contact-name').value = '';
                    document.getElementById('new-contact-email').value = '';
                    document.getElementById('new-contact-phone').value = '';
                    document.getElementById('new-contact-client').value = '';
                    $('#new-contact-client').trigger('change'); // Reset Select2
                } else {
                    alert('Erreur lors de l\'ajout du contact.');
                }
            });
        }
    });
</script>
