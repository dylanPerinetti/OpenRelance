document.addEventListener('DOMContentLoaded', function() {
    const dateEcheanceElement = document.getElementById('date-echeance');
    if (dateEcheanceElement) {
        const dateEcheance = new Date(dateEcheanceElement.textContent);
        dateEcheanceElement.textContent = dateEcheance.toLocaleDateString('fr-FR', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }

    loadComments();

    document.getElementById('add-comment-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const commentMessage = document.getElementById('comment-message').value;

        if (commentMessage.trim() === '') {
            showAlert('Veuillez entrer un commentaire.', 'danger');
            return;
        }

        fetch('action/add-comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                factures: [factureId],
                comment: commentMessage,
                userId: userId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Commentaire ajouté avec succès.', 'success');
                loadComments(); // Recharger les commentaires
            } else {
                showAlert('Erreur lors de l\'ajout du commentaire : ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'ajout du commentaire:', error);
            showAlert('Erreur lors de l\'ajout du commentaire.', 'danger');
        });
    });

    document.getElementById('add-relance-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const relanceType = document.getElementById('relance-type').value;
        const relanceDate = document.getElementById('relance-date').value;
        const relanceContact = document.getElementById('relance-contact').value;

        if (relanceType.trim() === '' || relanceDate.trim() === '' || relanceContact.trim() === '') {
            showAlert('Veuillez remplir tous les champs.', 'danger');
            return;
        }

        fetch('action/add-relance.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_factures: factureId,
                type_relance: relanceType,
                date_relance: relanceDate,
                id_contact_client: relanceContact,
                id_user_open_relance: userId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Relance programmée avec succès.', 'success');
                window.location.reload(); // Recharger la page pour voir la nouvelle relance
            } else {
                showAlert('Erreur lors de la programmation de la relance : ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur lors de la programmation de la relance:', error);
            showAlert('Erreur lors de la programmation de la relance.', 'danger');
        });
    });

    // Formater les montants en utilisant toLocaleString
    const montantElements = document.querySelectorAll('.montant');
    montantElements.forEach(element => {
        const montant = parseFloat(element.textContent.replace(/\s/g, '').replace(',', '.'));
        element.textContent = montant.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    });
});

function loadComments() {
    fetch(`action/get-comment.php?factureId=${factureId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const commentsList = document.querySelector('.comments-list ul');
                commentsList.innerHTML = ''; // Clear current comments

                data.commentaires.forEach(comment => {
                    const li = document.createElement('li');
                    li.innerHTML = `<p><strong class="comment-initials">${comment.initial_user_open_relance}</strong> (${new Date(comment.date_commentaire).toLocaleDateString('fr-FR', {
                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                    })}) :</p>
                                    <p>${comment.message_commentaire}</p>`;
                    commentsList.appendChild(li);
                });
            } else {
                showAlert('Erreur lors du chargement des commentaires : ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des commentaires:', error);
            showAlert('Erreur lors du chargement des commentaires.', 'danger');
        });
}

function showAlert(message, type) {
    const alertBox = document.createElement('div');
    alertBox.className = `alert alert-${type}`;
    alertBox.textContent = message;
    document.body.appendChild(alertBox);

    setTimeout(() => {
        alertBox.remove();
    }, 3000);
}
