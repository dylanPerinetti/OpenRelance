<?php
require_once 'connexion/mysql-db-config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');
    exit;
}

// Fonction pour récupérer le nombre de relances par date
function getRelancesParDate($month, $year) {
    try {
        $pdo = get_db_connection('read');
        $stmt = $pdo->prepare('
            SELECT date_relance, COUNT(*) as nb_relances
            FROM relance_client
            WHERE MONTH(date_relance) = :month AND YEAR(date_relance) = :year
            GROUP BY date_relance
        ');
        $stmt->execute(['month' => $month, 'year' => $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        log_error('Erreur lors de la récupération des relances : ' . $e->getMessage());
        return [];
    }
}

// Déterminer le mois et l'année en cours
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Récupérer les relances pour le mois et l'année en cours
$relances = getRelancesParDate($month, $year);
$relances_par_date = [];
foreach ($relances as $relance) {
    $relances_par_date[$relance['date_relance']] = $relance['nb_relances'];
}

// Calculer le nombre de jours dans le mois
$nb_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Créer un tableau pour le calendrier
$calendar = [];
for ($i = 1; $i <= $nb_days; $i++) {
    $date = "$year-$month-" . str_pad($i, 2, '0', STR_PAD_LEFT);
    $calendar[$i] = isset($relances_par_date[$date]) ? $relances_par_date[$date] : 0;
}

// Définir les mois précédents et suivants pour la navigation
$prev_month = $month - 1;
$next_month = $month + 1;
$prev_year = $year;
$next_year = $year;

if ($prev_month == 0) {
    $prev_month = 12;
    $prev_year--;
}
if ($next_month == 13) {
    $next_month = 1;
    $next_year++;
}

// Obtenir la date du jour pour encadrer
$today = date('Y-m-d');

// Obtenir le nom du mois et l'année en cours
$current_month = DateTime::createFromFormat('!m', $month)->format('F');
$current_year = $year;
?>

<!-- Ajout de la modal pour afficher les relances planifiées -->
<div id="relanceModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Relances Planifiées</h2>
        <div id="plannedRelances"></div>
    </div>
</div>


<div class="calendar-widget">
    <h2>Calendrier des Relances</h2>
    <div class="calendar-navigation">
        <a href="?month=<?= $prev_month ?>&year=<?= $prev_year ?>" class="nav-link">« Mois Précédent</a>
        <span><?= "$current_month $current_year" ?></span>
        <a href="?month=<?= $next_month ?>&year=<?= $next_year ?>" class="nav-link">Mois Suivant »</a>
    </div>
    <table class="calendar-table">
        <thead>
            <tr>
                <th>Lun</th>
                <th>Mar</th>
                <th>Mer</th>
                <th>Jeu</th>
                <th>Ven</th>
                <th>Sam</th>
                <th>Dim</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $first_day = date('N', strtotime("$year-$month-01"));
            $current_day = 1 - $first_day + 1;
            while ($current_day <= $nb_days) {
                echo '<tr>';
                for ($i = 1; $i <= 7; $i++) {
                    if ($current_day > 0 && $current_day <= $nb_days) {
                        $date_str = "$year-$month-" . str_pad($current_day, 2, '0', STR_PAD_LEFT);
                        $class_today = ($date_str == $today) ? ' today' : '';
                        echo "<td class='calendar-cell$class_today' data-date='$date_str'>$current_day<br><span class='relances-count'>{$calendar[$current_day]}</span></td>";
                    } else {
                        echo '<td></td>';
                    }
                    $current_day++;
                }
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    // Gestion de la modal
    var modal = document.getElementById("relanceModal");
    var span = document.getElementsByClassName("close")[0];

    // Ouvrir la modal lorsqu'une case du calendrier est cliquée
    document.querySelectorAll('.calendar-cell').forEach(cell => {
        cell.addEventListener('click', function() {
            var date = this.getAttribute('data-date');

            // Requête AJAX pour obtenir les relances planifiées pour cette date
            fetch('action/get-relances.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ date: date })
            })
            .then(response => response.json())
            .then(data => {
                var plannedRelancesDiv = document.getElementById('plannedRelances');
                plannedRelancesDiv.innerHTML = ''; // Vider le contenu précédent

                if (data.length > 0) {
                    // Regrouper les relances par ID de relance
                    var relancesMap = data.reduce((acc, relance) => {
                        if (!acc[relance.relance_id]) {
                            acc[relance.relance_id] = {
                                type_relance: relance.type_relance,
                                nom_client: relance.nom_client,
                                contact_client: relance.contact_client,
                                factures: []
                            };
                        }
                        if (relance.facture_id) {
                            acc[relance.relance_id].factures.push({
                                facture_id: relance.facture_id,
                                numeros_de_facture: relance.numeros_de_facture,
                                montant_facture: relance.montant_facture,
                                date_echeance_payment: relance.date_echeance_payment
                            });
                        }
                        return acc;
                    }, {});

                    // Afficher les relances et les factures associées
                    for (var relanceId in relancesMap) {
                        if (relancesMap.hasOwnProperty(relanceId)) {
                            var relanceInfo = document.createElement('div');
                            relanceInfo.innerHTML = `
                                <p>Type: ${relancesMap[relanceId].type_relance}</p>
                                <p>Client: ${relancesMap[relanceId].nom_client || 'N/A'}</p>
                                <p>Contact: ${relancesMap[relanceId].contact_client}</p>
                                <p>Factures:</p>
                            `;

                            var facturesList = document.createElement('ul');
                            relancesMap[relanceId].factures.forEach(facture => {
                                var factureItem = document.createElement('li');
                                factureItem.innerHTML = `<a href="facture.php?id=${facture.facture_id}">${facture.numeros_de_facture} - ${facture.montant_facture}€ - Échéance: ${facture.date_echeance_payment}</a>`;
                                facturesList.appendChild(factureItem);
                            });
                            
                            relanceInfo.appendChild(facturesList);
                            relanceInfo.innerHTML += '<hr>';
                            plannedRelancesDiv.appendChild(relanceInfo);
                        }
                    }
                } else {
                    plannedRelancesDiv.innerHTML = '<p>Aucune relance planifiée.</p>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });

            modal.style.display = "block";
        });
    });

    // Fermer la modal lorsqu'on clique sur la croix
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Fermer la modal lorsqu'on clique en dehors de la modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>


