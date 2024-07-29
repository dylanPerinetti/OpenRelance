document.addEventListener('DOMContentLoaded', function() {
    const confirmationModal = document.getElementById('confirmation-modal');
    const csvPreviewModal = document.getElementById('csv-preview-modal');
    const alertContainer = document.getElementById('alert-container');

    document.getElementById('import-csv-btn').addEventListener('click', () => document.getElementById('csv-file-input').click());
    document.getElementById('csv-file-input').addEventListener('change', handleFileSelect);
    document.getElementById('confirm-import-btn').addEventListener('click', confirmImportCSV);
    document.getElementById('cancel-import-btn').addEventListener('click', () => (csvPreviewModal.style.display = 'none'));
    document.getElementById('export-csv-btn').addEventListener('click', exportCSV);
    document.getElementById('new-client-parma').addEventListener('input', filterClients);
    document.getElementById('new-client-name').addEventListener('input', filterClients);
    document.getElementById('add-client-btn').addEventListener('click', prepareAddClient);
    document.getElementById('confirm-add-btn').addEventListener('click', addClient);
    document.querySelectorAll('.close').forEach(btn => btn.addEventListener('click', () => btn.closest('.modal').style.display = 'none'));
    window.addEventListener('click', event => {
        if (event.target === confirmationModal) confirmationModal.style.display = 'none';
        if (event.target === csvPreviewModal) csvPreviewModal.style.display = 'none';
    });

    fetchClients();

    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => previewCSV(e.target.result);
            reader.readAsText(file);
        }
    }

    function previewCSV(content) {
        const rows = content.split('\n');
        const header = rows[0].split(',').map(col => col.trim().toLowerCase());
        const previewTbody = document.querySelector('#csv-preview-table tbody');
        previewTbody.innerHTML = '';

        const parmaIndex = header.findIndex(col => col.includes('parma'));
        const nameIndex = header.findIndex(col => col.includes('client') || col.includes('entreprise') || col.includes('nom'));

        if (parmaIndex === -1 || nameIndex === -1) {
            showAlert('Le fichier CSV doit contenir des colonnes avec les mots "parma" et "client/entreprise/nom".', 'danger');
            return;
        }

        fetch('action/get-clients.php')
            .then(response => response.json())
            .then(existingClients => {
                rows.slice(1).forEach(row => {
                    const cols = row.split(',');
                    if (cols.length > Math.max(parmaIndex, nameIndex)) {
                        const clientName = cols[nameIndex].trim().toUpperCase();
                        const parmaNumber = cols[parmaIndex].trim();

                        if (clientName && parmaNumber && isNumber(parmaNumber)) {
                            const tr = document.createElement('tr');
                            tr.className = existingClients.some(client => client.nom_client === clientName) ? 'client-exists' :
                                existingClients.some(client => client.numeros_parma === parmaNumber) ? 'parma-exists' : '';
                            tr.innerHTML = `<td>${parmaNumber}</td><td>${clientName}</td>`;
                            previewTbody.appendChild(tr);
                        }
                    }
                });
                csvPreviewModal.style.display = 'block';
            });
    }

    function confirmImportCSV() {
        const formData = new FormData();
        formData.append('csv_file', document.getElementById('csv-file-input').files[0]);

        fetch('action/add-clients-csv.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(handleCSVImportResponse);
    }

    function handleCSVImportResponse(data) {
        if (data.success) {
            fetchClients();
            showAlert('Clients importés avec succès.', 'success');
        } else {
            data.errors.forEach(error => showAlert(error, 'danger'));
            data.warnings.forEach(warning => showWarning(warning));
        }
        csvPreviewModal.style.display = 'none';
    }

    function exportCSV() {
        fetch('action/export-clients.php')
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'clients.csv';
                document.body.appendChild(a);
                a.click();
                a.remove();
            });
    }

    function filterClients() {
        const name = document.getElementById('new-client-name').value.toUpperCase();
        const parma = document.getElementById('new-client-parma').value.replace(/\D/g, '');
        const rows = document.querySelectorAll('#clients-tbody tr');

        rows.forEach(row => {
            const tdParma = row.cells[0].textContent;
            const tdName = row.cells[1].textContent;
            row.style.display = (tdParma.includes(parma) && tdName.includes(name)) ? '' : 'none';
        });
    }

    function prepareAddClient() {
        const parma = document.getElementById('new-client-parma').value;
        const name = document.getElementById('new-client-name').value.toUpperCase();

        if (name && parma && isNumber(parma)) {
            fetch('action/check-client.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ numeros_parma: parma, nom_client: name })
            })
            .then(response => response.json())
            .then(data => handleClientCheck(data, name, parma));
        } else {
            showAlert('Veuillez remplir tous les champs et vérifier que le numéro de Parma est valide.', 'warning');
        }
    }

    function handleClientCheck(data, name, parma) {
        if (data.nameExists) {
            showAlert('Le nom du client existe déjà.', 'warning');
        } else if (data.exists) {
            showAlert(`Le numéro de Parma (${parma}) est déjà utilisé. Êtes-vous sûr de vouloir ajouter le client ${name} ?`, 'warning');
            confirmationModal.style.display = 'block';
        } else {
            addClient(name, parma);
        }
    }

    function addClient(name, parma) {
        fetch('action/add-client.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nom_client: name, numeros_parma: parma })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchClients();
                showAlert('Client ajouté avec succès.', 'success');
                document.getElementById('new-client-name').value = '';
                document.getElementById('new-client-parma').value = '';
                confirmationModal.style.display = 'none';
            } else {
                showAlert('Erreur lors de l\'ajout du client.', 'danger');
            }
        });
    }

    function fetchClients() {
        fetch('action/get-clients.php')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('clients-tbody');
                tbody.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(client => {
                        fetch('action/get-user-initial.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id_user: client.id_user_open_relance })
                        })
                        .then(response => response.json())
                        .then(userData => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${client.numeros_parma}</td>
                                <td>${client.nom_client}</td>
                                <td>${userData.initial_user_open_relance}</td>
                            `;
                            row.addEventListener('click', () => window.location.href = `client.php?id=${client.id}`);
                            tbody.appendChild(row);
                        });
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="3">Aucun client trouvé</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                const tbody = document.getElementById('clients-tbody');
                tbody.innerHTML = '<tr><td colspan="3">Erreur lors de la récupération des données</td></tr>';
            });
    }

    function showAlert(message, type = 'danger') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${type}`;
        alertDiv.innerHTML = `<span class="closebtn">&times;</span>${message}`;
        alertContainer.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.style.opacity = '0';
            setTimeout(() => alertDiv.remove(), 600);
        }, 5000);

        alertDiv.querySelector('.closebtn').addEventListener('click', () => alertDiv.remove());
    }

    function showWarning(warning) {
        const warningDiv = document.createElement('div');
        warningDiv.className = 'alert warning';
        warningDiv.innerHTML = `<span class="closebtn">&times;</span>${warning}`;
        const confirmBtn = document.createElement('button');
        confirmBtn.className = 'confirmbtn';
        confirmBtn.innerText = 'Oui';
        warningDiv.appendChild(confirmBtn);
        alertContainer.appendChild(warningDiv);

        confirmBtn.addEventListener('click', () => warningDiv.remove());

        setTimeout(() => {
            warningDiv.style.opacity = '0';
            setTimeout(() => warningDiv.remove(), 600);
        }, 5000);
    }

    function isNumber(value) {
        return /^\d+$/.test(value);
    }
});
