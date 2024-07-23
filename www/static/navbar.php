<nav class="navbar">
    <div class="navbar-logo">
        <a href="index.php"><img src="img/LogoSansTitreOpenRelance.png" alt="Logo"></a>
    </div>
    <div class="navbar-links">
        <a href="clients.php">Clients</a>
        <a href="contact.php">Contact</a>
        <a href="factures.php">Factures</a>
        <a href="rappel.php">Rappel</a>
    </div>
    <div class="navbar-search">
        <input type="text" placeholder="Rechercher...">
        <i class="fas fa-search"></i>
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

<script>
    // Gestion du thème
    const themeToggle = document.getElementById('theme-toggle');
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    document.body.classList.add(currentTheme);
    themeToggle.innerHTML = currentTheme === 'light' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';

    themeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark');
        const theme = document.body.classList.contains('dark') ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        themeToggle.innerHTML = theme === 'light' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
    });

    // Gestion du modal de connexion
    const authButton = document.getElementById('auth-button');
    const modal = document.getElementById('auth-modal');
    const cancelButton = document.querySelector('.cancelbtn');

    if (authButton) {
        authButton.addEventListener('click', () => {
            modal.style.display = 'block';
        });

        cancelButton.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    }
</script>
