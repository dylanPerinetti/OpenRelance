document.addEventListener('DOMContentLoaded', function() {
    let selectedFactures = new Set();
    let selectionMode = false;

    fetchClientInfo();
    fetchClientFactures();
    fetchClientContacts();
    fetchClientRelances();

    function fetchClientInfo() {
        fetch('action/get-client-info.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ client_id: clientId })
        })
        .then(response => response.json())
        .then(data => {
            const clientInfoDiv = document.getElementById('client-info');
            if (data) {
                clientInfoDiv.innerHTML = `
                <p><strong>Nom du Client:</strong> <a href="client.php?id=${clientId}">${data.nom_client}</a></p>
                <p><strong>Numéro de Parma:</strong> ${data.numeros_parma}</p>
                <p><strong>Ajouté par:</strong> ${data.initial_user_open_relance}</p>
                `;
            } else {
                clientInfoDiv.innerHTML = '<p>Aucune information trouvée pour ce client.</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching client info:', error);
            showAlert('Erreur lors de la récupération des informations du client.', 'danger');
        });
    }

    function fetchClientFactures() {
        fetch('action/get-client-factures.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ client_id: clientId })
        })
        .then(response => response.json())
        .then(data => {
            const impayeesTbody = document.getElementById('factures-impayees-tbody');
            const payeesTbody = document.getElementById('factures-payees-tbody');
            let totalDue = 0;
            const today = new Date();

            impayeesTbody.innerHTML = ''; // Clear the loading message
            payeesTbody.innerHTML = ''; // Clear the loading message

            if (data.length > 0) {
                let unpaidCount = 0;
                data.forEach(facture => {
                    const row = document.createElement('tr');
                    let statusColor = '';

                    if (facture.montant_reste_a_payer == 0) {
                        statusColor = 'green';
                    } else if (facture.montant_reste_a_payer < 0) {
                        statusColor = 'purple';
                    } else {
                        const dueDate = new Date(facture.date_echeance_payment);
                        if (dueDate < today) {
                            statusColor = 'orange';
                        } else {
                            statusColor = 'blue';
                        }
                    }

                    row.innerHTML = `
                    <td style="text-align:center;"><input type="checkbox" class="select-facture" data-id="${facture.id}"></td>
                    <td style="text-align:center;"><span class="status-dot" style="background-color: ${statusColor};"></span></td>
                    <td>${facture.numeros_de_facture}</td>
                    <td>${formatDate(facture.date_emission_facture)}</td>
                    <td>${formatDate(facture.date_echeance_payment)}</td>
                    <td style="text-align:right;">${formatMontant(facture.montant_facture)} €</td>
                    <td style="text-align:right;">${formatMontant(facture.montant_reste_a_payer)} €</td>
                    `;
                    row.addEventListener('click', function(event) {
                        if (selectionMode) {
                            event.stopPropagation();
                            const checkbox = row.querySelector('.select-facture');
                            checkbox.checked = !checkbox.checked;
                            if (checkbox.checked) {
                                selectedFactures.add(facture.id);
                            } else {
                                selectedFactures.delete(facture.id);
                            }
                        } else {
                            window.location.href = `facture.php?id=${facture.id}`;
                        }
                    });

                    if (facture.montant_reste_a_payer != 0) {
                        impayeesTbody.appendChild(row);
                        totalDue += parseFloat(facture.montant_reste_a_payer);
                        unpaidCount++;
                    } else {
                        row.removeChild(row.lastChild); // Remove the "Montant à Payer Restant" column
                        payeesTbody.appendChild(row);
                    }
                });

                document.getElementById('total-due').innerHTML = `Total dû à ce jour: <strong>${formatMontant(totalDue)} €</strong>`;
                document.getElementById('unpaid-count').innerHTML = `Il y a <strong>${unpaidCount} </strong> facture(s) impayée(s) à ce jour.`;

                // Sort paid invoices by emission date
                const payeesRows = Array.from(payeesTbody.querySelectorAll('tr'));
                payeesRows.sort((a, b) => {
                    const dateA = new Date(a.children[2].innerText.split('/').reverse().join('-'));
                    const dateB = new Date(b.children[2].innerText.split('/').reverse().join('-'));
                    return dateB - dateA;
                });
                payeesRows.forEach(row => payeesTbody.appendChild(row));
            } else {
                impayeesTbody.innerHTML = '<tr><td colspan="7">Aucune facture impayée trouvée</td></tr>';
                payeesTbody.innerHTML = '<tr><td colspan="6">Aucune facture payée trouvée</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error fetching factures:', error);
            showAlert('Erreur lors de la récupération des factures', 'danger');
        });
    }

    function fetchClientContacts() {
        fetch('action/get-client-contacts.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ client_id: clientId })
        })
        .then(response => response.json())
        .then(data => {
            const contactsTbody = document.getElementById('contacts-tbody');
            contactsTbody.innerHTML = '';
            if (data.length > 0) {
                const numerosParma = data[0].numeros_parma;
                const nomClient = data[0].nom_client; 
                data.forEach(contact => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td>${contact.nom_contactes_clients}</td>
                    <td>${contact.fonction_contactes_clients}</td>
                    <td><span class="telephone" onclick="copyToClipboard('${contact.telphone_contactes_clients}')">${contact.telphone_contactes_clients}</span></td>
                    <td><a href="mailto:${contact.mail_contactes_clients}?subject=${numerosParma}- ${nomClient} - Relance">${contact.mail_contactes_clients}</a></td>
                    `;
                    contactsTbody.appendChild(row);
                });
            } else {
                contactsTbody.innerHTML = '<tr><td colspan="4">Aucun contact trouvé</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error fetching contacts:', error);
            showAlert('Erreur lors de la récupération des contacts', 'danger');
        });
    }

    function fetchClientRelances() {
        fetch('action/get-relances-by-client.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ client_id: clientId })
        })
        .then(response => response.json())
        .then(data => {
            const relanceDiv = document.getElementById('relance-info');
            relanceDiv.innerHTML = ''; // Clear the loading message

            if (data.length > 0) {
                const today = new Date();
                const sortedRelances = data.sort((a, b) => new Date(a.date_relance) - new Date(b.date_relance));
                sortedRelances.slice(-3).forEach(relance => {
                    const relanceDate = new Date(relance.date_relance);
                    const diffTime = Math.abs(relanceDate - today);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    const dayName = relanceDate.toLocaleDateString('fr-FR', { weekday: 'long', day: '2-digit', month: 'long' });
                    let message;

                    if (relanceDate > today) {
                        message = `La prochaine relance est prévue par ${relance.type_relance} dans ${diffDays} jours, pour le ${dayName}.`;
                    } else if (relanceDate < today) {
                        message = `Dernière relance par ${relance.type_relance} il y a ${diffDays} jours, le ${dayName} dernier.`;
                    } else {
                        message = `Relance par ${relance.type_relance} prévue pour aujourd'hui, le ${dayName}.`;
                    }

                    const factureList = relance.factures.map(f => `Facture ${f.numeros_de_facture} (${formatMontant(f.montant_facture)} €)`).join(', ');
                    relanceDiv.innerHTML += `${message}<br>Factures concernées: ${factureList}<br><br>`;
                });
            } else {
                relanceDiv.innerHTML = '<p>Aucune relance prévue.</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching relances:', error);
            showAlert('Erreur lors de la récupération des relances', 'danger');
        });
    }

    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('#factures-impayees-tbody .select-facture');
        const isChecked = this.checked;
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
            if (isChecked) {
                selectedFactures.add(checkbox.dataset.id);
            } else {
                selectedFactures.delete(checkbox.dataset.id);
            }
        });
    });

    document.getElementById('select-all-paid').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('#factures-payees-tbody .select-facture');
        const isChecked = this.checked;
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
            if (isChecked) {
                selectedFactures.add(checkbox.dataset.id);
            } else {
                selectedFactures.delete(checkbox.dataset.id);
            }
        });
    });

    document.getElementById('factures-impayees-tbody').addEventListener('change', function(event) {
        if (event.target.classList.contains('select-facture')) {
            const factureId = event.target.dataset.id;
            if (event.target.checked) {
                selectedFactures.add(factureId);
            } else {
                selectedFactures.delete(factureId);
            }
        }
    });

    document.getElementById('factures-payees-tbody').addEventListener('change', function(event) {
        if (event.target.classList.contains('select-facture')) {
            const factureId = event.target.dataset.id;
            if (event.target.checked) {
                selectedFactures.add(factureId);
            } else {
                selectedFactures.delete(factureId);
            }
        }
    });

    document.getElementById('add-comment-btn').addEventListener('click', function() {
        if (selectedFactures.size > 0) {
            document.getElementById('comment-modal').style.display = 'block';
        } else {
            showAlert('Veuillez sélectionner au moins une facture.', 'warning');
        }
    });

    document.getElementById('save-comment-btn').addEventListener('click', function() {
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
                document.getElementById('comment-modal').style.display = 'none';
                fetchClientFactures();
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error adding comment:', error);
            showAlert("Erreur lors de l'ajout du commentaire.", 'danger');
        });
    });

    document.getElementById('mark-as-paid-btn').addEventListener('click', function() {
        if (selectedFactures.size > 0) {
            const selectedFactureIds = Array.from(selectedFactures);

            fetch('action/mark-as-paid.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ factures: selectedFactureIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert("Facture(s) marquée(s) comme payée(s) avec succès.", 'success');
                    fetchClientFactures();
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error marking as paid:', error);
                showAlert("Erreur lors de la mise à jour des factures.", 'danger');
            });
        } else {
            showAlert("Veuillez sélectionner au moins une facture.", 'warning');
        }
    });

    document.getElementById('select-toggle-btn').addEventListener('click', () => {
        selectionMode = !selectionMode;
        document.querySelectorAll('.select-facture').forEach(checkbox => {
            checkbox.style.display = selectionMode ? 'inline-block' : 'none';
        });
        document.getElementById('select-all').style.display = selectionMode ? 'inline-block' : 'none';
        document.getElementById('select-all-paid').style.display = selectionMode ? 'inline-block' : 'none';
    });

    document.getElementById('add-relance-btn').addEventListener('click', function() {
        if (selectedFactures.size > 0) {
            document.getElementById('relance-modal').style.display = 'block';
        } else {
            showAlert('Veuillez sélectionner au moins une facture.', 'warning');
        }
    });

    document.getElementById('save-relance-btn').addEventListener('click', function() {
        const relanceType = document.getElementById('relance-type').value;
        const relanceDate = document.getElementById('relance-date').value;
        const contactClientId = document.getElementById('contact-client').value;
        const relanceComment = document.getElementById('relance-comment').value;

        if (relanceType.trim() === '' || relanceDate.trim() === '') {
            showAlert("Les champs type de relance et date de relance doivent être remplis.", 'warning');
            return;
        }

        const selectedFactureIds = Array.from(selectedFactures);

        fetch('action/add-relance-factures.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                factures: selectedFactureIds, 
                relanceType: relanceType, 
                relanceDate: relanceDate, 
                contactId: contactClientId || null, 
                commentaire: relanceComment, 
                userId: userId 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert("Relance ajoutée avec succès.", 'success');
                document.getElementById('relance-modal').style.display = 'none';
                fetchClientFactures();
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error adding relance:', error);
            showAlert("Erreur lors de l'ajout de la relance.", 'danger');
        });
    });

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        if (!isNaN(date)) {
            return date.toLocaleDateString('fr-FR');
        }
        return 'Date invalide';
    }

    function formatMontant(montant) {
        return Number(montant).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

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

    document.getElementById('download-pdf').addEventListener('click', function() {
        html2pdf().from(document.querySelector('.widget-content')).save('widget.pdf');
    });

    // Close modals
    document.querySelectorAll('.modal .close').forEach(closeBtn => {
        closeBtn.addEventListener('click', () => {
            closeBtn.closest('.modal').style.display = 'none';
        });
    });

    window.addEventListener('click', (event) => {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    });
});
