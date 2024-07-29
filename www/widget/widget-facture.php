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
    <script>
        const factureId = <?php echo json_encode($facture['id']); ?>;
        const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
    </script>
    <script src="scripts/script-facture.js"></script>
</div>
