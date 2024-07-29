document.addEventListener('DOMContentLoaded', function() {
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
            document.getElementById('client-info').innerHTML = '<p>Erreur lors de la récupération des informations du client.</p>';
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

            impayeesTbody.innerHTML = ''; // Clear the loading message
            payeesTbody.innerHTML = ''; // Clear the loading message

            if (data.length > 0) {
                let unpaidCount = 0;
                data.forEach(facture => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td>${facture.numeros_de_facture}</td>
                    <td>${formatDate(facture.date_emission_facture)}</td>
                    <td>${formatDate(facture.date_echeance_payment)}</td>
                    <td style="text-align:right;">${formatMontant(facture.montant_facture)} €</td>
                    <td style="text-align:right;">${formatMontant(facture.montant_reste_a_payer)} €</td>
                    `;
                    row.addEventListener('click', function() {
                        window.location.href = `facture.php?id=${facture.id}`;
                    });

                    if (facture.montant_reste_a_payer > 0) {
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
                    const dateA = new Date(a.children[1].innerText.split('/').reverse().join('-'));
                    const dateB = new Date(b.children[1].innerText.split('/').reverse().join('-'));
                    return dateB - dateA;
                });
                payeesRows.forEach(row => payeesTbody.appendChild(row));
            } else {
                impayeesTbody.innerHTML = '<tr><td colspan="5">Aucune facture impayée trouvée</td></tr>';
                payeesTbody.innerHTML = '<tr><td colspan="4">Aucune facture payée trouvée</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error fetching factures:', error);
            document.getElementById('factures-impayees-tbody').innerHTML = '<tr><td colspan="5">Erreur lors de la récupération des factures</td></tr>';
            document.getElementById('factures-payees-tbody').innerHTML = '<tr><td colspan="4">Erreur lors de la récupération des factures</td></tr>';
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
            document.getElementById('contacts-tbody').innerHTML = '<tr><td colspan="4">Erreur lors de la récupération des contacts</td></tr>';
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
            document.getElementById('relance-info').innerHTML = '<p>Erreur lors de la récupération des relances.</p>';
        });
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        if (!isNaN(date)) {
            return date.toLocaleDateString('fr-FR');
        }
        return 'Date invalide';
    }

    function formatMontant(montant) {
        return montant.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Numéro de téléphone copié dans le presse-papiers');
        }).catch(err => {
            console.error('Erreur lors de la copie du numéro de téléphone:', err);
        });
    }

    document.getElementById('download-pdf').addEventListener('click', function() {
        html2pdf().from(document.querySelector('.widget-content')).save('widget.pdf');
    });
});
