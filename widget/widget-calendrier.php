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

<!-- Ajout de la modal pour enregistrer une relance -->
<div id="relanceModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Enregistrer une Relance</h2>
        <form id="relanceForm">
            <label for="type_relance">Type de Relance:</label>
            <select name="type_relance" id="type_relance">
                <option value="mail">Mail</option>
                <option value="appel">Appel</option>
                <option value="courrier1">Courrier 1</option>
                <option value="courrier2">Courrier 2</option>
                <option value="recommande">Recommandé</option>
            </select>
            <br>
            <label for="date_relance">Date de Relance:</label>
            <input type="date" id="date_relance" name="date_relance" readonly>
            <br>
            <label for="nom_client">Nom Client (non obligatoire):</label>
            <input type="text" id="nom_client" name="nom_client">
            <br>
            <label for="contact_client">Contact Client:</label>
            <input type="text" id="contact_client" name="contact_client" required>
            <br>
            <button type="submit">Enregistrer</button>
        </form>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script>
    // Gestion de la modal
    var modal = document.getElementById("relanceModal");
    var span = document.getElementsByClassName("close")[0];

    // Ouvrir la modal lorsqu'une case du calendrier est cliquée
    document.querySelectorAll('.calendar-cell').forEach(cell => {
        cell.addEventListener('click', function() {
            var date = this.getAttribute('data-date');
            document.getElementById('date_relance').value = date;
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

    // Autocomplétion pour le champ "Contact Client"
    $("#contact_client").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: 'action/search-contacts.php',
                type: 'POST',
                dataType: 'json',
                data: JSON.stringify({ search: request.term }),
                contentType: 'application/json',
                success: function(data) {
                    response($.map(data, function(item) {
                        return {
                            label: item.nom_contactes_clients,
                            value: item.nom_contactes_clients,
                            id: item.id
                        };
                    }));
                }
            });
        },
        select: function(event, ui) {
            $('#contact_client').data('selected-id', ui.item.id);
        },
        minLength: 2
    });

    // Soumettre le formulaire
    document.getElementById('relanceForm').addEventListener('submit', function(event) {
        event.preventDefault();
        
        var formData = new FormData(this);
        formData.append('id_contact_client', $('#contact_client').data('selected-id'));
        
        fetch('action/add-relance.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Relance enregistrée avec succès!');
                modal.style.display = "none";
                // Optionnel: mettre à jour le calendrier ou effectuer d'autres actions
            } else {
                alert('Erreur lors de l\'enregistrement de la relance.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    });
</script>
