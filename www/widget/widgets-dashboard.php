<?php
$user_initiales = $_SESSION['user_initiales'];
$user_id = $_SESSION['user_id'];
?>

<div class="widget-content widget-full">
    <h1>Tableau de bord</h1>
</div>

<div class="widget-container">
    <div class="widget-content">
        <canvas id="chart1"></canvas>
    </div>
    <div class="widget-content">
        <canvas id="chart2"></canvas>
    </div>
    <div class="widget-content">
        <canvas id="chart3"></canvas>
    </div>
    <div class="widget-content" >
        <canvas id="chart4"></canvas>
    </div>
</div>


<script>
    var userInitiales = "<?php echo $user_initiales; ?>";
    var userId = "<?php echo $user_id; ?>";
</script>
<script src="scripts/script-dashboard.js"></script>