<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/widget.css">
    <link rel="icon" type="image/x-icon" href="/img/favicon.ico">
    <title>Clients</title>
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <?php include 'static/navbar.php'?>
    <div class="widget-content">
        <?php include 'widget/widget-clients.php'?>
    </div>
    <?php include 'static/footer.php'?>
</body>
</html>
