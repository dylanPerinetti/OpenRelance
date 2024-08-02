document.addEventListener('DOMContentLoaded', function() {
    fetchContacts();

    const addContactBtn = document.getElementById('add-contact-btn');
    const importContactsBtn = document.getElementById('import-contacts-btn');
    const exportContactsBtn = document.getElementById('export-contacts-btn');
    const contactsCsvFile = document.getElementById('contacts-csv-file');
    const contactModal = document.getElementById('contact-modal');
    const closeModalBtn = document.querySelector('.close');
    const editContactBtn = document.getElementById('edit-contact-btn');
    const saveContactBtn = document.getElementById('save-contact-btn');

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

    importContactsBtn.addEventListener('click', function() {
        contactsCsvFile.click();
    });

    contactsCsvFile.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const content = e.target.result;
                importContactsCSV(content);
            };
            reader.readAsText(file);
        }
    });

    exportContactsBtn.addEventListener('click', function() {
        fetch('action/export-contacts.php')
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'contacts.csv';
            document.body.appendChild(a);
            a.click();
            a.remove();
        });
    });

    closeModalBtn.addEventListener('click', function() {
        contactModal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === contactModal) {
            contactModal.style.display = 'none';
        }
    });

    editContactBtn.addEventListener('click', function() {
        enableEditMode();
    });

    saveContactBtn.addEventListener('click', function() {
        const id = document.getElementById('contact-id').value;
        const fonction = document.getElementById('contact-fonction').value;
        const nom = document.getElementById('contact-nom').value;
        const email = document.getElementById('contact-email').value;
        const telephone = document.getElementById('contact-telephone').value;

        updateContact(id, fonction, nom, email, telephone);
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
                        row.dataset.contactId = contact.id;
                        row.innerHTML = `
                        <td>${contact.numeros_parma}</td>
                        <td>${contact.nom_client}</td>
                        <td>${contact.nom_contactes_clients}</td>
                        <td>${contact.fonction_contactes_clients}</td>
                        <td class="copy-to-clipboard" data-phone="${contact.telphone_contactes_clients}">${contact.telphone_contactes_clients}</td>
                        <td><a href="mailto:${contact.mail_contactes_clients}?cc=fabien.mathely@volvo.com&subject=${contact.numeros_parma}- ${contact.nom_client} - Relance">${contact.mail_contactes_clients}</a></td>
                        <td>${userData ? userData.initial_user_open_relance : 'N/A'}</td>
                        `;
                        row.addEventListener('click', function() {
                            showContactModal(contact);
                        });
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
                showAlert(data.message || 'Erreur lors de l\'ajout du contact.', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'ajout du contact:', error);
            showAlert('Erreur lors de l\'ajout du contact.', 'danger');
        });
    }

    function showContactModal(contact) {
        document.getElementById('contact-id').value = contact.id;
        document.getElementById('contact-fonction').value = contact.fonction_contactes_clients;
        document.getElementById('contact-nom').value = contact.nom_contactes_clients;
        document.getElementById('contact-email').value = contact.mail_contactes_clients;
        document.getElementById('contact-telephone').value = contact.telphone_contactes_clients;
        document.getElementById('contact-client').value = contact.nom_client;
        document.getElementById('contact-parma').value = contact.numeros_parma;

        disableEditMode();
        contactModal.style.display = 'block';
    }

    function enableEditMode() {
        document.getElementById('contact-fonction').readOnly = false;
        document.getElementById('contact-nom').readOnly = false;
        document.getElementById('contact-email').readOnly = false;
        document.getElementById('contact-telephone').readOnly = false;
        editContactBtn.style.display = 'none';
        saveContactBtn.style.display = 'inline-block';
    }

    function disableEditMode() {
        document.getElementById('contact-fonction').readOnly = true;
        document.getElementById('contact-nom').readOnly = true;
        document.getElementById('contact-email').readOnly = true;
        document.getElementById('contact-telephone').readOnly = true;
        editContactBtn.style.display = 'inline-block';
        saveContactBtn.style.display = 'none';
    }

    function updateContact(id, fonction, nom, email, telephone) {
        fetch('action/update-contact.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id,
                fonction_contactes_clients: fonction,
                nom_contactes_clients: nom,
                mail_contactes_clients: email,
                telphone_contactes_clients: telephone
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchContacts();
                disableEditMode();
                contactModal.style.display = 'none';
                showAlert('Contact mis à jour avec succès.', 'success');
            } else {
                showAlert(data.message || 'Erreur lors de la mise à jour du contact.', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur lors de la mise à jour du contact:', error);
            showAlert('Erreur lors de la mise à jour du contact.', 'danger');
        });
    }

    function importContactsCSV(content) {
        const rows = content.split('\n').slice(1).map(row => row.split(','));
        const contacts = rows.map(row => ({
            numeros_parma: row[0].trim(),
            nom_client: row[1].trim(),
            nom_contactes_clients: row[2].trim(),
            fonction_contactes_clients: row[3].trim(),
            telphone_contactes_clients: row[4].trim(),
            mail_contactes_clients: row[5].trim()
        }));
        fetch('action/import-contacts.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ contacts: contacts })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchContacts();
                showAlert('Contacts importés avec succès.', 'success');
            } else {
                showAlert(data.message || 'Erreur lors de l\'importation des contacts.', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'importation des contacts:', error);
            showAlert('Erreur lors de l\'importation des contacts.', 'danger');
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
