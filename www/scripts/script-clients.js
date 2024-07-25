document.addEventListener('DOMContentLoaded', function() {
    fetch('action/get-clients.php')
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('clients-tbody');
        tbody.innerHTML = ''; // Clear the loading message
        if (data.length > 0) {
            data.forEach(client => {
                fetch('action/get-user-initial.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id_user: client.id_user_open_relance })
                })
                .then(response => response.json())
                .then(userData => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td>${client.id}</td>
                    <td>${client.nom_client}</td>
                    <td>${client.numeros_parma}</td>
                    <td>${userData.initial_user_open_relance}</td>
                    `;
                    tbody.appendChild(row);
                });
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4">Aucun client trouvé</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        const tbody = document.getElementById('clients-tbody');
        tbody.innerHTML = '<tr><td colspan="4">Erreur lors de la récupération des données</td></tr>';
    });

    const confirmationModal = document.getElementById('confirmation-modal');
    const closeModal = document.querySelector('.close');
    const confirmAddBtn = document.getElementById('confirm-add-btn');
    const cancelAddBtn = document.getElementById('cancel-add-btn');

    document.getElementById('new-client-parma').addEventListener('input', function(event) {
        this.value = this.value.replace(/\D/g, '');
    });

    document.getElementById('new-client-name').addEventListener('input', function(event) {
        this.value = this.value.toUpperCase();
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            const name = document.getElementById('new-client-name').value;
            const parma = document.getElementById('new-client-parma').value;

            if (name && parma) {
                fetch('action/check-client.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ numeros_parma: parma })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        confirmationModal.style.display = 'block';
                    } else {
                        addClient(name, parma);
                    }
                });
            } else {
                alert('Veuillez remplir tous les champs.');
            }
        }
    });

    confirmAddBtn.addEventListener('click', function() {
        const name = document.getElementById('new-client-name').value;
        const parma = document.getElementById('new-client-parma').value;
        addClient(name, parma);
        confirmationModal.style.display = 'none';
    });

    cancelAddBtn.addEventListener('click', function() {
        confirmationModal.style.display = 'none';
    });

    closeModal.addEventListener('click', function() {
        confirmationModal.style.display = 'none';
    });

    function addClient(name, parma) {
        fetch('action/add-client.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ nom_client: name, numeros_parma: parma })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetch('action/get-user-initial.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id_user: userId })
                })
                .then(response => response.json())
                .then(userData => {
                    const tbody = document.getElementById('clients-tbody');
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td>${data.new_id}</td>
                    <td>${name}</td>
                    <td>${parma}</td>
                    <td>${userData.initial_user_open_relance}</td>
                    `;
                    tbody.appendChild(row);
                    document.getElementById('new-client-name').value = '';
                    document.getElementById('new-client-parma').value = '';
                });
            } else {
                alert('Erreur lors de l\'ajout du client.');
            }
        });
    }
});
