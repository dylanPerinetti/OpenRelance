<div class="widget-content">
    <h1>Détails de la Facture</h1>
    <div class="facture-details">
        <div class="box gauge-box">
            <div class="gauge-container" style="--percentage: <?php echo (1 - ($facture['montant_reste_a_payer'] / $facture['montant_facture'])) * 100; ?>%;">
                <div class="gauge-text">
                    <?php echo number_format($facture['montant_facture'], 2, ',', ' '); ?> €
                    <div class="remaining">
                        Restant: <?php echo number_format($facture['montant_reste_a_payer'], 2, ',', ' '); ?> €
                    </div>
                </div>
            </div>
        </div>
        <div class="box info-box">
            <p><strong>Numéro de Facture :</strong> <?php echo htmlspecialchars($facture['numeros_de_facture']); ?></p>
            <p><strong>Date d'Échéance :</strong> <span id="date-echeance"><?php echo htmlspecialchars($facture['date_echeance_payment']); ?></span></p>
            <p><strong>Montant Facture :</strong> <span class="montant"><?php echo number_format($facture['montant_facture'], 2, ',', ' '); ?></span> €</p>
            <p><strong>Montant Restant à Payer:</strong> <span class="montant"><?php echo number_format($facture['montant_reste_a_payer'], 2, ',', ' '); ?></span> €</p>
            <p><strong>Client :</strong> <a href="client.php?id=<?=$facture['id_clients']?>"><?php echo htmlspecialchars($facture['nom_client']); ?></a></p>
        </div>
    </div>
    <?php if ($facture['montant_reste_a_payer'] > 0): ?>
        <h2>Programmer un Rappel</h2>
        <button id="open-modal-btn">Ajouter un Rappel</button>
    <?php endif; ?>

    <h2>Commentaires</h2>
    <div class="comments-list">
        <ul>
            <!-- Les commentaires seront chargés ici par le script JavaScript -->
        </ul>
    </div>

    <form id="add-comment-form">
        <textarea id="comment-message" rows="4" placeholder="Ajouter un commentaire..." required></textarea>
        <button type="submit">Ajouter</button>
    </form>
    <script>
        const factureId = <?php echo json_encode($facture['id']); ?>;
        const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
    </script>
    <script src="scripts/script-facture.js"></script>
</div>
    <!-- Modal pour ajouter des relances -->
    <div id="relance-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Ajouter une relance</h2>
            <label for="relance-type">Type de relance:</label>
            <select id="relance-type" class="form-input" required>
                <option value="Reglement à recevoir">Consulter les Règlements</option>
                <option value="Appel">Appel</option>
                <option value="mail">Mail</option>
                <option value="courier 1">Courier 1</option>
                <option value="courier 2">Courier 2</option>
                <option value="recommandé">Recommandé</option>
                <option value="litige">Litige</option>
            </select>
            <label for="contact-client">Contact Client (optionnel):</label>
            <select id="contact-client" class="form-input">
                <option value="">Sélectionner un contact (optionnel)</option>
                <?php foreach ($contacts as $contact): ?>
                    <option value="<?php echo $contact['id']; ?>">
                        <?php echo htmlspecialchars($contact['fonction_contactes_clients'] . ' - ' . $contact['nom_contactes_clients']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="relance-date">Date de relance:</label>
            <input type="date" id="relance-date" class="form-input" required>
            <label for="relance-comment">Commentaire:</label>
            <textarea id="relance-comment" rows="4" cols="50" placeholder="Ajouter votre commentaire ici..." class="form-input"></textarea>
            <button id="save-relance-btn" class="form-button">Enregistrer</button>
        </div>
    </div>