document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('show-import-balance-form').addEventListener('click', function() {
        document.getElementById('import-balance-form').style.display = 'block';
        this.style.display = 'none';
    });

    document.getElementById('cancel-import-balance').addEventListener('click', function() {
        document.getElementById('import-balance-form').style.display = 'none';
        document.getElementById('show-import-balance-form').style.display = 'block';
        resetImportForm();
    });

    function resetImportForm() {
        document.getElementById('balance-csv-file').value = '';
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

    document.getElementById('import-balance-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const fileInput = document.getElementById('balance-csv-file');
        const file = fileInput.files[0];
        
        if (!file) {
            showAlert('Veuillez sélectionner un fichier.', 'warning');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const content = e.target.result;
            processCSV(content);
        };
        reader.readAsText(file);
    });

    function processCSV(content) {
        const delimiter = content.includes(';') ? ';' : ',';
        const rows = content.split('\n').filter(row => row.trim() !== '');
        const header = rows[0].split(delimiter).map(col => col.trim());
        const requiredHeaders = ["Customer Number", "Customer Name", "Document Date", "Reference", "Due Date", "Currency", "Amount in Local Cur"];

        if (!requiredHeaders.every(col => header.includes(col))) {
            showAlert('L\'en-tête du fichier CSV est incorrect. Les en-têtes requis sont : Customer Number, Customer Name, Document Date, Reference, Due Date, Currency, Amount in Local Cur.', 'danger');
            return;
        }

        const previewTbody = document.querySelector('#balance-preview-table tbody');
        previewTbody.innerHTML = '';

        const dataRows = rows.slice(1).map(row => parseCSVRow(row, delimiter));
        const references = dataRows.map(row => row[header.indexOf('Reference')]);

        if (references.length === 0) {
            showAlert('Le fichier CSV ne contient aucune référence de facture.', 'warning');
            return;
        }

        fetch('action/check-balance-refs.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ references: references })
        })
        .then(response => response.json())
        .then(existingRefs => {
            const validRows = dataRows.filter(row => existingRefs.some(ref => ref.numeros_de_facture === row[header.indexOf('Reference')]));
            validRows.forEach(row => {
                const amount = parseFloat(row[header.indexOf('Amount in Local Cur')].replace(/"/g, '').replace(/\s/g, '').replace(/,/g, '.'));
                const documentDate = convertDateFormat(row[header.indexOf('Document Date')]);
                const dueDate = convertDateFormat(row[header.indexOf('Due Date')]);
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row[header.indexOf('Customer Number')]}</td>
                    <td>${row[header.indexOf('Customer Name')].toUpperCase()}</td>
                    <td>${documentDate}</td>
                    <td>${row[header.indexOf('Reference')]}</td>
                    <td>${dueDate}</td>
                    <td>${amount.toFixed(2)}</td>
                `;
                previewTbody.appendChild(tr);
            });

            if (validRows.length === 0) {
                showAlert('Aucune facture valide trouvée dans le fichier CSV.', 'warning');
                return;
            }

            document.getElementById('balance-preview-modal').style.display = 'block';

            document.getElementById('confirm-import-preview').addEventListener('click', function() {
                importBalance(validRows.map(row => ({
                    customerNumber: row[header.indexOf('Customer Number')],
                    customerName: row[header.indexOf('Customer Name')].toUpperCase(),
                    documentDate: convertDateFormat(row[header.indexOf('Document Date')]),
                    reference: row[header.indexOf('Reference')],
                    dueDate: convertDateFormat(row[header.indexOf('Due Date')]),
                    amount: parseFloat(row[header.indexOf('Amount in Local Cur')].replace(/"/g, '').replace(/\s/g, '').replace(/,/g, '.'))
                })), existingRefs);
            });

            document.getElementById('cancel-import-preview').addEventListener('click', function() {
                document.getElementById('balance-preview-modal').style.display = 'none';
            });
        })
        .catch(error => {
            console.error('Erreur lors de la vérification des références:', error);
            showAlert('Erreur lors de la vérification des références.', 'danger');
        });
    }

    function parseCSVRow(row, delimiter) {
        const result = [];
        let insideQuote = false;
        let value = '';

        for (let char of row) {
            if (char === '"') {
                insideQuote = !insideQuote;
            } else if (char === delimiter && !insideQuote) {
                result.push(value.trim());
                value = '';
            } else {
                value += char;
            }
        }
        result.push(value.trim());

        return result;
    }

    function convertDateFormat(dateStr) {
        const [day, month, year] = dateStr.split('.');
        return `${year}-${month}-${day}`;
    }

    function importBalance(dataRows, existingRefs) {
        const referencesInCSV = dataRows.map(row => row.reference);
        fetch('action/add-balance-csv.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ data: dataRows, existingRefs: existingRefs })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Balance importée avec succès!', 'success');
                document.getElementById('balance-preview-modal').style.display = 'none';
                setTimeout(() => location.reload(), 5100); // Refresh the page after 5.1 seconds
            } else {
                showAlert(data.message || 'Erreur lors de l\'importation de la balance.', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'importation de la balance:', error);
            showAlert('Erreur lors de l\'importation de la balance.', 'danger');
        });
    }
});
