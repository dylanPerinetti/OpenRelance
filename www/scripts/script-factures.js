document.addEventListener('DOMContentLoaded', function() {
    let facturesData = [];
    let selectedFactures = new Set();
    let currentPage = 1;
    let itemsPerPage = parseInt(document.getElementById('items-per-page').value, 10);
    let selectionMode = false;
    let sortColumn = null;
    let sortDirection = 'asc';

    const fetchFactures = () => {
        fetch('action/get-factures.php')
            .then(response => response.json())
            .then(data => {
                facturesData = data;
                applyFiltersAndRender();
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                showAlert('Erreur lors de la récupération des données', 'danger');
            });
    };

    const applyFiltersAndRender = () => {
        const searchValue = document.getElementById('search').value.toLowerCase();
        const statusFilter = document.getElementById('status-filter').value;
        const dateEcheanceFilter = document.getElementById('date-echeance-filter').value;
        itemsPerPage = parseInt(document.getElementById('items-per-page').value, 10);

        let filteredData = facturesData;

        if (searchValue) {
            filteredData = filteredData.filter(facture => {
                return Object.values(facture).some(value => 
                    value.toString().toLowerCase().includes(searchValue)
                );
            });
        }

        if (statusFilter === 'non-paye') {
            filteredData = filteredData.filter(facture => facture.montant_reste_a_payer > 0);
        } else if (statusFilter === 'paye') {
            filteredData = filteredData.filter(facture => facture.montant_reste_a_payer == 0);
        }

        if (dateEcheanceFilter) {
            filteredData = filteredData.filter(facture => new Date(facture.date_echeance_payment) <= new Date(dateEcheanceFilter));
        }

        if (sortColumn) {
            filteredData.sort((a, b) => {
                if (a[sortColumn] < b[sortColumn]) return sortDirection === 'asc' ? -1 : 1;
                if (a[sortColumn] > b[sortColumn]) return sortDirection === 'asc' ? 1 : -1;
                return 0;
            });
        }

        renderFactures(filteredData);
    };

    const renderFactures = (data) => {
        const tbody = document.getElementById('factures-tbody');
        tbody.innerHTML = '';
        
        const paginatedData = data.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);
        
        if (paginatedData.length > 0) {
            paginatedData.forEach(facture => {
                const statusColor = facture.montant_reste_a_payer == 0 ? 'green' : 'orange';
                const row = document.createElement('tr');
                row.classList.add('facture-row');
                row.dataset.href = `facture.php?id=${facture.id}`;
                row.dataset.client = facture.id_clients;
                row.innerHTML = `
                    <td style="display: ${selectionMode ? 'table-cell' : 'none'};"><input type="checkbox" class="select-facture" data-id="${facture.id}" data-client="${facture.id_clients}"></td>
                    <td><span style="color: ${statusColor};">&#9679;</span></td>
                    <td>${facture.numeros_parma}</td>
                    <td>${facture.nom_client}</td>
                    <td>${facture.numeros_de_facture}</td>
                    <td>${formatDate(facture.date_emission_facture)}</td>
                    <td>${formatDate(facture.date_echeance_payment)}</td>
                    <td style="text-align:right;">${formatMontant(facture.montant_facture)}€</td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="9">Aucune facture trouvée</td></tr>';
        }

        renderPagination(data.length);
    };

    const formatMontant = (montant) => {
        return Number(montant).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    const formatDate = (dateStr) => {
        const date = new Date(dateStr);
        if (!isNaN(date)) {
            return date.toLocaleDateString('fr-FR');
        }
        return 'Date invalide';
    };

    const renderPagination = (totalItems) => {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        
        const paginationTop = document.getElementById('pagination-top');
        const paginationBottom = document.getElementById('pagination-bottom');
        
        paginationTop.innerHTML = '';
        paginationBottom.innerHTML = '';

        const createPageButton = (page) => {
            const pageButton = document.createElement('button');
            pageButton.innerText = page;
            pageButton.classList.add('page-button');
            pageButton.dataset.page = page;

            if (page === currentPage) {
                pageButton.classList.add('active');
            }

            pageButton.addEventListener('click', () => {
                currentPage = page;
                applyFiltersAndRender();
            });

            return pageButton;
        };

        if (totalPages > 1) {
            const addEllipsis = () => {
                const ellipsis = document.createElement('span');
                ellipsis.innerText = '...';
                return ellipsis;
            };

            paginationTop.appendChild(createPageButton(1));
            paginationBottom.appendChild(createPageButton(1));

            if (currentPage > 4) {
                paginationTop.appendChild(addEllipsis());
                paginationBottom.appendChild(addEllipsis());
            }

            for (let i = Math.max(2, currentPage - 2); i <= Math.min(totalPages - 1, currentPage + 2); i++) {
                paginationTop.appendChild(createPageButton(i));
                paginationBottom.appendChild(createPageButton(i));
            }

            if (currentPage < totalPages - 3) {
                paginationTop.appendChild(addEllipsis());
                paginationBottom.appendChild(addEllipsis());
            }

            if (totalPages > 1) {
                paginationTop.appendChild(createPageButton(totalPages));
                paginationBottom.appendChild(createPageButton(totalPages));
            }
        }
    };

    document.getElementById('search').addEventListener('input', applyFiltersAndRender);
    document.getElementById('status-filter').addEventListener('change', applyFiltersAndRender);
    document.getElementById('date-echeance-filter').addEventListener('change', applyFiltersAndRender);
    document.getElementById('items-per-page').addEventListener('change', () => {
        currentPage = 1; // Reset to first page when items per page change
        applyFiltersAndRender();
    });

    document.getElementById('factures-tbody').addEventListener('click', function(event) {
        if (!selectionMode) {
            const tr = event.target.closest('tr');
            if (tr && tr.classList.contains('facture-row')) {
                window.location.href = tr.dataset.href;
            }
        }
    });

    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.select-facture');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            if (this.checked) {
                selectedFactures.add(checkbox.dataset.id);
            } else {
                selectedFactures.delete(checkbox.dataset.id);
            }
        });
        validateSelectedFactures();
    });

    document.getElementById('factures-tbody').addEventListener('change', function(event) {
        if (event.target.classList.contains('select-facture')) {
            const factureId = event.target.dataset.id;
            const clientId = event.target.dataset.client;
            if (event.target.checked) {
                const clientIds = new Set(Array.from(selectedFactures).map(id => {
                    const checkbox = document.querySelector(`.select-facture[data-id="${id}"]`);
                    return checkbox ? checkbox.dataset.client : null;
                }).filter(id => id !== null));

                if (clientIds.size > 1) {
                    showAlert("Vous ne pouvez sélectionner que des factures du même client pour ajouter une relance groupée.", 'warning');
                    event.target.checked = false;
                } else {
                    selectedFactures.add(factureId);
                    fetchContactsForClient(clientId);
                }
            } else {
                selectedFactures.delete(factureId);
                validateSelectedFactures();
            }
        }
    });

    const fetchContactsForClient = (clientId) => {
        fetch(`action/get-contacts-by-idclient.php?client_id=${clientId}`)
            .then(response => response.json())
            .then(data => {
                const contactSelect = document.getElementById('contact-client');
                contactSelect.innerHTML = '<option value="">Sélectionner un contact</option>';
                data.forEach(contact => {
                    const option = document.createElement('option');
                    option.value = contact.id;
                    option.textContent = `${contact.nom_contactes_clients} - ${contact.mail_contactes_clients}`;
                    contactSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching contacts:', error);
                showAlert('Erreur lors de la récupération des contacts', 'danger');
            });
    };

    const commentModal = document.getElementById('comment-modal');
    const commentBtn = document.getElementById('add-comment-btn');
    const saveCommentBtn = document.getElementById('save-comment-btn');
    const closeModal = commentModal.querySelector('.close');
    
    commentBtn.addEventListener('click', () => {
        if (selectedFactures.size > 0) {
            commentModal.style.display = 'block';
        } else {
            showAlert("Veuillez sélectionner au moins une facture.", 'warning');
        }
    });

    closeModal.addEventListener('click', () => {
        commentModal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target == commentModal) {
            commentModal.style.display = 'none';
        }
    });

    saveCommentBtn.addEventListener('click', () => {
        const commentText = document.getElementById('comment-text').value;
        if (commentText.trim() === '') {
            showAlert("Le commentaire ne peut pas être vide.", 'warning');
            return;
        }

        const selectedFactureIds = Array.from(selectedFactures);

        fetch('action/add-comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ factures: selectedFactureIds, comment: commentText, userId: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert("Commentaire ajouté avec succès.", 'success');
                commentModal.style.display = 'none';
                fetchFactures();
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error adding comment:', error);
            showAlert("Erreur lors de l'ajout du commentaire.", 'danger');
        });
    });

    document.getElementById('select-toggle-btn').addEventListener('click', () => {
        selectionMode = !selectionMode;
        document.getElementById('select-all').parentElement.style.display = selectionMode ? 'table-cell' : 'none';
        document.getElementById('select-all').style.display = selectionMode ? 'table-cell' : 'none';
        applyFiltersAndRender();
    });

    const relanceModal = document.getElementById('relance-modal');
    const relanceBtn = document.getElementById('add-relance-btn');
    const saveRelanceBtn = document.getElementById('save-relance-btn');
    const closeRelanceModal = relanceModal.querySelector('.close');
    
    relanceBtn.addEventListener('click', () => {
        if (selectedFactures.size > 0) {
            relanceModal.style.display = 'block';
        } else {
            showAlert("Veuillez sélectionner au moins une facture.", 'warning');
        }
    });

    closeRelanceModal.addEventListener('click', () => {
        relanceModal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target == relanceModal) {
            relanceModal.style.display = 'none';
        }
    });

    saveRelanceBtn.addEventListener('click', () => {
        const relanceType = document.getElementById('relance-type').value;
        const relanceDate = document.getElementById('relance-date').value;
        const contactClientId = document.getElementById('contact-client').value;

        if (relanceType.trim() === '' || relanceDate.trim() === '' || contactClientId === '') {
            showAlert("Tous les champs de la relance doivent être remplis.", 'warning');
            return;
        }

        const selectedFactureIds = Array.from(selectedFactures);

        fetch('action/add-relance-factures.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ factures: selectedFactureIds, relanceType: relanceType, relanceDate: relanceDate, contactId: contactClientId, userId: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert("Relance ajoutée avec succès.", 'success');
                relanceModal.style.display = 'none';
                fetchFactures();
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error adding relance:', error);
            showAlert("Erreur lors de l'ajout de la relance.", 'danger');
        });
    });

    // Ajout de la fonctionnalité de tri
    document.querySelectorAll('.factures-table th').forEach(header => {
        header.addEventListener('click', () => {
            const column = header.dataset.column;
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }
            applyFiltersAndRender();
        });
    });

    fetchFactures();

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
});
