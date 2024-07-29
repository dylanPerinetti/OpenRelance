document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('show-add-facture-form').addEventListener('click', function() {
        document.getElementById('add-facture-form').style.display = 'block';
        this.style.display = 'none';
    });

    document.getElementById('cancel-add-facture').addEventListener('click', function() {
        document.getElementById('add-facture-form').style.display = 'none';
        document.getElementById('show-add-facture-form').style.display = 'block';
        resetAddForm();
    });

    document.getElementById('show-export-form').addEventListener('click', function() {
        document.getElementById('export-facture-form').style.display = 'block';
        this.style.display = 'none';
    });

    document.getElementById('cancel-export-facture').addEventListener('click', function() {
        document.getElementById('export-facture-form').style.display = 'none';
        document.getElementById('show-export-form').style.display = 'block';
        resetExportForm();
    });

    function resetAddForm() {
        document.getElementById('new-facture-number').value = '';
        document.getElementById('new-facture-date-emission').value = '';
        document.getElementById('new-facture-date').value = '';
        document.getElementById('new-facture-amount').value = '';
        document.getElementById('new-facture-paid').value = '0';
        document.getElementById('new-facture-client').value = '';
    }

    function resetExportForm() {
        document.getElementById('export-client').value = '';
        document.getElementById('export-status').value = 'all';
        document.getElementById('export-format').value = 'csv';
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

        alertDiv.querySelector('.closebtn').addEventListener('click', function() {
            const div = this.parentElement;
            div.style.opacity = '0';
            setTimeout(() => div.remove(), 600);
        });
    }

    function autocompleteClient() {
        $("#new-facture-client, #export-client").autocomplete({
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
                $(event.target).data('selected-id', ui.item.id);
            }
        });
    }

    autocompleteClient();

    document.getElementById('new-facture-amount').addEventListener('input', function(event) {
        this.value = this.value.replace(/[^\d.]/g, '');
    });

    document.getElementById('new-facture-paid').addEventListener('input', function(event) {
        this.value = this.value.replace(/[^\d.]/g, '');
    });

    document.getElementById('add-facture-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const factureNumber = document.getElementById('new-facture-number').value;
        const factureDateEmission = document.getElementById('new-facture-date-emission').value;
        const factureDate = document.getElementById('new-facture-date').value;
        const factureAmount = document.getElementById('new-facture-amount').value;
        const facturePaid = document.getElementById('new-facture-paid').value;
        const factureClient = $('#new-facture-client').data('selected-id');

        if (factureNumber && factureDateEmission && factureDate && factureAmount && facturePaid && factureClient) {
            addFacture(factureNumber, factureDateEmission, factureDate, factureAmount, facturePaid, factureClient);
        } else {
            showAlert('Veuillez remplir tous les champs.', 'warning');
        }
    });

    function addFacture(factureNumber, factureDateEmission, factureDate, factureAmount, facturePaid, factureClient) {
        fetch('action/add-facture.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                numeros_de_facture: factureNumber,
                date_emission_facture: factureDateEmission,
                date_echeance_payment: factureDate,
                montant_facture: factureAmount,
                montant_reste_a_payer: factureAmount - facturePaid,
                id_clients: factureClient
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Facture ajoutée avec succès!', 'success');
                setTimeout(() => location.reload(), 5100); // Refresh the page after 5.1 seconds
            } else if (data.message === 'Facture exists') {
                showAlert('Le numéro de facture existe déjà.', 'warning');
            } else {
                showAlert('Erreur lors de l\'ajout de la facture.', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'ajout de la facture:', error);
            showAlert('Erreur lors de l\'ajout de la facture.', 'danger');
        });
    }

    document.getElementById('export-facture-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const exportClient = document.getElementById('export-client').value;
        const exportStatus = document.getElementById('export-status').value;
        const exportFormat = document.getElementById('export-format').value;

        exportFactures(exportClient, exportStatus, exportFormat);
    });

    function exportFactures(client, status, format) {
        fetch('action/export-factures.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                client: client,
                status: status,
                format: format
            })
        })
        .then(response => response.blob())
        .then(blob => {
            const filter = status === 'all' ? 'All' : status.charAt(0).toUpperCase() + status.slice(1);
            const clientName = client ? client : 'Global';
            const filename = `Factures_${filter}_${clientName}.${format}`;
            
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            showAlert('Exportation réussie!', 'success');
        })
        .catch(error => {
            console.error('Erreur lors de l\'exportation des factures:', error);
            showAlert('Erreur lors de l\'exportation des factures.', 'danger');
        });
    }
});
