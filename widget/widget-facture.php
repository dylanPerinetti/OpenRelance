<div class="widget-content">
    <h1>Détails de la Facture</h1>
    <div class="facture-details">
        <div class="box gauge-box">
            <div class="gauge-container" style="--percentage: <?php echo (1 - ($facture['montant_reste_a_payer'] / $facture['montant_facture'])) * 100; ?>%;">
                <div class="gauge-text">
                    <?php echo htmlspecialchars($facture['montant_facture']);?> €
                    <div class="remaining">
                        Restant: <?php echo htmlspecialchars($facture['montant_reste_a_payer']); ?> €
                    </div>
                </div>
            </div>
        </div>
        <div class="box info-box">
            <p><strong>Numéro de Facture :</strong> <?php echo htmlspecialchars($facture['numeros_de_facture']); ?></p>
            <p><strong>Date d'Échéance :</strong> <?php echo htmlspecialchars($facture['date_echeance_payment']); ?></p>
            <p><strong>Montant Facture :</strong> <?php echo htmlspecialchars($facture['montant_facture']); ?> €</p>
            <p><strong>Montant Restant :</strong> <?php echo htmlspecialchars($facture['montant_reste_a_payer']); ?> €</p>
            <p><strong>Client :</strong> <?php echo htmlspecialchars($facture['nom_client']); ?></p>
        </div>
    </div>

    <h2>Commentaires</h2>
    <div class="comments-list">
        <?php if (count($commentaires) > 0): ?>
            <ul>
                <?php foreach ($commentaires as $commentaire): ?>
                    <li>
                        <p><strong><?php echo htmlspecialchars($commentaire['initial_user_open_relance']); ?></strong> (<?php echo htmlspecialchars($commentaire['date_commentaire']); ?>) :</p>
                        <p><?php echo htmlspecialchars($commentaire['message_commentaire']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun commentaire pour cette facture.</p>
        <?php endif; ?>
    </div>

    <h2>Ajouter un Commentaire</h2>
    <form id="add-comment-form">
        <textarea id="comment-message" rows="4" placeholder="Ajouter un commentaire..." required></textarea>
        <button type="submit">Ajouter</button>
    </form>

    <?php if ($facture['montant_reste_a_payer'] > 0): ?>
        <h2>Programmer une Relance</h2>
        <form id="add-relance-form">
            <label for="relance-type">Type de Relance :</label>
            <select id="relance-type" required>
                <option value="">Sélectionner un type de relance</option>
                <option value="mail">Mail</option>
                <option value="appel">Appel</option>
                <option value="courrier 1">Courrier 1</option>
                <option value="courrier 2">Courrier 2</option>
                <option value="recommandé">Recommandé</option>
            </select>
            <label for="relance-contact">Contact :</label>
            <select id="relance-contact" required>
                <option value="">Sélectionner un contact</option>
                <?php foreach ($contacts as $contact): ?>
                    <option value="<?php echo $contact['id']; ?>">
                        <?php echo htmlspecialchars($contact['fonction_contactes_clients'] . ' - ' . $contact['nom_contactes_clients']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="relance-date">Date de Relance :</label>
            <input type="date" id="relance-date" required>
            <button type="submit">Ajouter</button>
        </form>
    <?php endif; ?>

    <script src="scripts/script-facture.js"></script>
</div>
