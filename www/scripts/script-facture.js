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
                document.getElementById('comment-message').value = ''; // Clear the input field
            } else {
                showAlert('Erreur lors de l\'ajout du commentaire : ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'ajout du commentaire:', error);
            showAlert('Erreur lors de l\'ajout du commentaire.', 'danger');
        });
    });

    // Modal handling
    const modal = document.getElementById('relance-modal');
    const openModalBtn = document.getElementById('open-modal-btn');
    const closeModalBtn = document.querySelector('.modal .close');
    const saveRelanceBtn = document.getElementById('save-relance-btn');

    openModalBtn.addEventListener('click', function() {
        modal.style.display = 'block';
    });

    closeModalBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });

    // Form submission for relance
    saveRelanceBtn.addEventListener('click', function(event) {
        event.preventDefault();

        const relanceType = document.getElementById('relance-type').value;
        const relanceDate = document.getElementById('relance-date').value;
        const relanceContact = document.getElementById('contact-client').value;
        const relanceCommentaire = document.getElementById('relance-comment').value;

        if (relanceType.trim() === '' || relanceDate.trim() === '') {
            showAlert('Veuillez remplir les champs obligatoires.', 'danger');
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
                id_contact_client: relanceContact || null,
                commentaire: relanceCommentaire,
                id_user_open_relance: userId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Relance programmée avec succès.', 'success');
                modal.style.display = 'none'; // Fermer la modal
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
                    li.innerHTML = `
                        <p><strong class="comment-initials">${comment.initial_user_open_relance}</strong> (${new Date(comment.date_commentaire).toLocaleDateString('fr-FR', {
                            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                        })}) :</p>
                        <p class="comment-text" data-id="${comment.id}">${comment.message_commentaire}</p>
                        <i class="fas fa-pencil-alt edit-comment" data-id="${comment.id}"></i>
                    `;
                    commentsList.appendChild(li);
                });

                // Ajouter l'écouteur d'événements pour l'édition des commentaires
                document.querySelectorAll('.edit-comment').forEach(element => {
                    element.addEventListener('click', function() {
                        const commentId = this.dataset.id;
                        const commentTextElement = this.previousElementSibling;
                        const originalText = commentTextElement.textContent;

                        // Remplacer le texte par un champ de texte éditable
                        commentTextElement.innerHTML = `<input type="text" class="edit-input" value="${originalText}">`;
                        const inputElement = commentTextElement.querySelector('.edit-input');
                        inputElement.focus();

                        // Transformer l'icône de crayon en bouton de validation
                        this.classList.remove('fa-pencil-alt');
                        this.classList.add('fa-check', 'save-comment');

                        const saveButton = this;

                        inputElement.addEventListener('blur', function() {
                            const newText = inputElement.value;

                            // Envoyer la modification au serveur
                            fetch('action/update-comment.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ id: commentId, message: newText })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    commentTextElement.textContent = newText;
                                    showAlert('Commentaire mis à jour avec succès.', 'success');
                                } else {
                                    commentTextElement.textContent = originalText;
                                    showAlert('Erreur lors de la mise à jour du commentaire.', 'danger');
                                }
                            })
                            .catch(error => {
                                console.error('Erreur lors de la mise à jour du commentaire:', error);
                                commentTextElement.textContent = originalText;
                                showAlert('Erreur lors de la mise à jour du commentaire.', 'danger');
                            })
                            .finally(() => {
                                // Remettre l'icône de validation en crayon
                                saveButton.classList.remove('fa-check', 'save-comment');
                                saveButton.classList.add('fa-pencil-alt');
                            });
                        });
                    });
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