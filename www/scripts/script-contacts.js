// Fonction d'affichage des alertes
function showAlert(message, type = 'danger') {
    const alertContainer = document.getElementById('alert-container');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${type}`;
    alertDiv.innerHTML = `<span class="closebtn">&times;</span>${message}`;

    alertContainer.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 600);
    }, 5000);

    const closeBtn = alertDiv.querySelector('.closebtn');
    closeBtn.addEventListener('click', function() {
        const div = this.parentElement;
        div.style.opacity = '0';
        setTimeout(() => div.remove(), 600);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    fetchContacts();

    const addContactBtn = document.getElementById('add-contact-btn');

    document.getElementById('new-contact-parma').addEventListener('input', function() {
        filterContacts();
        autocompleteParma(); // Update autocomplete for parma numbers
    });
    document.getElementById('new-contact-client').addEventListener('input', filterContacts);
    document.getElementById('new-contact-name').addEventListener('input', filterContacts);
    document.getElementById('new-contact-function').addEventListener('change', filterContacts);
    document.getElementById('new-contact-phone').addEventListener('input', function(event) {
        this.value = this.value.replace(/\D/g, '');
        filterContacts();
    });
    document.getElementById('new-contact-email').addEventListener('input', filterContacts);

    addContactBtn.addEventListener('click', function() {
        const parma = document.getElementById('new-contact-parma').value;
        const client = document.getElementById('new-contact-client').value;
        const name = document.getElementById('new-contact-name').value;
        const functionContact = document.getElementById('new-contact-function').value;
        const phone = document.getElementById('new-contact-phone').value;
        const email = document.getElementById('new-contact-email').value;

        if (parma && client && name && functionContact && phone && email) {
            addContact(parma, client, name, functionContact, phone, email);
        } else {
            showAlert('Veuillez remplir tous les champs.', 'warning');
        }
    });

    function fetchContacts() {
        fetch('action/get-contacts.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('contacts-tbody');
            tbody.innerHTML = ''; // Clear the loading message
            if (data.length > 0) {
                data.forEach(contact => {
                    fetch('action/get-user-initial.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id_user: contact.id_user_open_relance })
                    })
                    .then(response => response.json())
                    .then(userData => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                        <td>${contact.numeros_parma}</td>
                        <td>${contact.nom_client}</td>
                        <td>${contact.nom_contactes_clients}</td>
                        <td>${contact.fonction_contactes_clients}</td>
                        <td class="copy-to-clipboard" data-phone="${contact.telphone_contactes_clients}">${contact.telphone_contactes_clients}</td>
                        <td><a href="mailto:${contact.mail_contactes_clients}?subject=${contact.numeros_parma}- ${contact.nom_client} - Relance">${contact.mail_contactes_clients}</a></td>
                        <td>${userData ? userData.initial_user_open_relance : 'N/A'}</td>
                        `;
                        tbody.appendChild(row);
                    });
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="7">Aucun contact trouvé</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            const tbody = document.getElementById('contacts-tbody');
            tbody.innerHTML = '<tr><td colspan="7">Erreur lors de la récupération des données</td></tr>';
        });
    }

    function filterContacts() {
        const parma = document.getElementById('new-contact-parma').value;
        const client = document.getElementById('new-contact-client').value.toUpperCase();
        const name = document.getElementById('new-contact-name').value.toUpperCase();
        const functionContact = document.getElementById('new-contact-function').value;
        const phone = document.getElementById('new-contact-phone').value;
        const email = document.getElementById('new-contact-email').value.toUpperCase();

        const rows = document.querySelectorAll('#contacts-tbody tr');
        rows.forEach(row => {
            const tdParma = row.cells[0].textContent;
            const tdClient = row.cells[1].textContent.toUpperCase();
            const tdName = row.cells[2].textContent.toUpperCase();
            const tdFunction = row.cells[3].textContent;
            const tdPhone = row.cells[4].textContent;
            const tdEmail = row.cells[5].textContent.toUpperCase();

            if (tdParma.includes(parma) && tdClient.includes(client) && tdName.includes(name) && tdFunction.includes(functionContact) && tdPhone.includes(phone) && tdEmail.includes(email)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function addContact(parma, client, name, functionContact, phone, email) {
        fetch('action/add-contact.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                numeros_parma: parma,
                nom_client: client, 
                nom_contactes_clients: name, 
                fonction_contactes_clients: functionContact, 
                telphone_contactes_clients: phone, 
                mail_contactes_clients: email,
                id_user_open_relance: userId // Ensure the current user's ID is sent
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchContacts();
                document.getElementById('new-contact-parma').value = '';
                document.getElementById('new-contact-client').value = '';
                document.getElementById('new-contact-name').value = '';
                document.getElementById('new-contact-function').value = 'Comptable';
                document.getElementById('new-contact-phone').value = '';
                document.getElementById('new-contact-email').value = '';
                showAlert('Contact ajouté avec succès.', 'success');
            } else {
                showAlert('Erreur lors de l\'ajout du contact.', 'danger');
            }
        });
    }

    function autocompleteClient() {
        $("#new-contact-client").autocomplete({
            source: function(request, response) {
                const parma = document.getElementById('new-contact-parma').value;
                $.ajax({
                    url: 'action/search-clients.php',
                    type: 'POST',
                    dataType: 'json',
                    data: JSON.stringify({ search: request.term, parma: parma }),
                    contentType: 'application/json',
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.nom_client,
                                value: item.nom_client,
                                id: item.id,
                                parma: item.numeros_parma
                            };
                        }));
                    }
                });
            },
            select: function(event, ui) {
                $('#new-contact-client').data('selected-id', ui.item.id);
                $('#new-contact-parma').val(ui.item.parma);
                filterContacts(); // Update filtering based on selection
            }
        });
    }

    function autocompleteParma() {
        $("#new-contact-parma").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: 'action/search-clients-by-parma.php',
                    type: 'POST',
                    dataType: 'json',
                    data: JSON.stringify({ search: request.term }),
                    contentType: 'application/json',
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.numeros_parma + ' - ' + item.nom_client,
                                value: item.numeros_parma,
                                id: item.id,
                                client: item.nom_client
                            };
                        }));
                    }
                });
            },
            select: function(event, ui) {
                $('#new-contact-parma').data('selected-id', ui.item.id);
                $('#new-contact-client').val(ui.item.client);
                filterContacts(); // Update filtering based on selection
            }
        });
    }

    autocompleteClient();
    autocompleteParma();

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('copy-to-clipboard')) {
            const phone = event.target.dataset.phone;
            navigator.clipboard.writeText(phone).then(() => {
                showAlert('Numéro de téléphone copié dans le presse-papiers.', 'success');
            });
        }
    });
});
