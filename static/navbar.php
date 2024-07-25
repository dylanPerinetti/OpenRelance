<nav class="navbar">
    <div class="navbar-logo">
        <a href="index.php"><img src="img/LogoSansTitreOpenRelance.png" alt="Logo"></a>
    </div>
    <div class="navbar-links">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <a href="clients.php">Clients</a>
            <a href="contact.php">Contact</a>
            <a href="factures.php">Factures</a>
            <a href="Calendrier.php">Rappels</a>
        <?php endif; ?>
    </div>
    <div class="navbar-search">
        <input type="text" id="search-input" placeholder="Rechercher..." autocomplete="off">
        <i class="fas fa-search"></i>
        <div id="search-results" class="search-results"></div>
    </div>
    <div class="theme-switcher">
        <button id="theme-toggle"><i class="fas fa-moon"></i></button>
    </div>
    <div class="navbar-auth">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <form action="action/logout.php" method="post">
                <button type="submit" class="auth-button">Déconnexion</button>
            </form>
        <?php else: ?>
            <button id="auth-button" class="auth-button">Connexion</button>
        <?php endif; ?>
    </div>
</nav>

<!-- Titre de la page -->
<div class="page-name">
        <h1><?=$page_name?></h1>
    </div>

<!-- Modal pour la connexion -->
<div id="auth-modal" class="modal">
    <form class="modal-content" action="action/login.php" method="post">
        <div class="container">
            <h1>Connexion</h1>
            <p>Veuillez remplir ce formulaire pour vous connecter.</p>
            <hr>
            <label for="email"><b>Email</b></label>
            <input type="text" placeholder="Entrer Email" name="email" required>

            <label for="psw"><b>Mot de passe</b></label>
            <input type="password" placeholder="Entrer Mot de passe" name="psw" required>

            <label>
                <input type="checkbox" checked="checked" name="remember"> Se souvenir de moi
            </label>

            <div class="clearfix">
                <button type="button" class="cancelbtn">Annuler</button>
                <button type="submit" class="signupbtn">Connexion</button>
            </div>
        </div>
    </form>
</div>

<!-- Modal pour les résultats de recherche complète -->
<div id="full-search-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="full-search-results"></div>
    </div>
</div>

<script src="scripts/script-navbar.js"></script>