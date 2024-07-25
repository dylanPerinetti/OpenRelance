document.getElementById('add-comment-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const commentMessage = document.getElementById('comment-message').value;
    
    if (commentMessage.trim() === '') {
        alert('Veuillez entrer un commentaire.');
        return;
    }

    fetch('action/add-comment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id_factures: factureId,
            message_commentaire: commentMessage
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Commentaire ajouté avec succès.');
            window.location.reload(); // Recharger la page pour voir le nouveau commentaire
        } else {
            alert('Erreur lors de l\'ajout du commentaire.');
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'ajout du commentaire:', error);
        alert('Erreur lors de l\'ajout du commentaire.');
    });
});

document.getElementById('add-relance-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const relanceType = document.getElementById('relance-type').value;
    const relanceDate = document.getElementById('relance-date').value;
    const relanceContact = document.getElementById('relance-contact').value;

    if (relanceType.trim() === '' || relanceDate.trim() === '' || relanceContact.trim() === '') {
        alert('Veuillez remplir tous les champs.');
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
            alert('Relance programmée avec succès.');
            window.location.reload(); // Recharger la page pour voir la nouvelle relance
        } else {
            alert('Erreur lors de la programmation de la relance : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur lors de la programmation de la relance:', error);
        alert('Erreur lors de la programmation de la relance.');
    });
});

const factureId = <?php echo json_encode($facture_id); ?>;
const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
