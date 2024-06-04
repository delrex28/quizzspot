<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <title>Organisation</title>
</head>
<body>
    <header>
        <h2><img src="images/icon-quizzspot.png"><a href="https://www.quizzspot.fr">Quizz:Spot</a></h2>
        
        <nav>
            <a href="https://www.quizzspot.fr">Accueil</a>
            <a href="https://orga.quizzspot.fr">Espace Orga</a>
            <a href="https://admin.quizzspot.fr">Espace Administrateur</a>
            <a href="https://bilan.quizzspot.fr">Espace Apprenant</a>
        </nav>

        <div class="searchbar">
            <input type="search" placeholder="Rechercher un Quizz">
            <button><ion-icon name="search-outline"></ion-icon></button>
        </div>

        <div class="menu">
            <ion-icon name="menu-outline"></ion-icon>
        </div>
    </header>
    <section>
        <div class="form-box">
            <div class="form-value">
                <form action="verification.php" method="post">
                    <h2>Se connecter</h2>
                    <div class="input-box">
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="email" name="email" required>
                        <label for="">Email</label>
                    </div>

                    <div class="input-box">
                        <ion-icon name="eye-off-outline"></ion-icon>
                        <input type="password" name="password" required>
                        <label for="">Mot de passe</label>
                    </div>
                    <div class="forget">
                        <label for="">
                            <input type="checkbox">Se rappeler de moi
                            <a href="#">Mot de passe oublié?</a>
                        </label>
                    </div>

                    <button type="submit">Connexion</button>
                    <?php
                        if(isset($_GET['erreur'])){
                            $err = $_GET['erreur'];
                            if($err==1 || $err==2)
                                echo "<p style='color:yellow'>Utilisateur ou mot de passe incorrect</p>";
                        }
                    ?>
                </form>
            </div>
        </div>
    </section>
    <footer>
        <ul class="social_icon">
            <li><a href="https://github.com/delrex28/quizzspot.git"><ion-icon name="logo-github"></ion-icon></a></li>
        </ul>
        <ul class="footer_menu">
            <li><a href="#">Accueil</a></li>
            <li><a href="#">À propos</a></li>
            <li><a href="#">L'équipe</a></li>
            <li><a href="#">Nous contacter</a></li>
        </ul>
        <p>@2024 Quizzspot | Tous droits réservés</p>
    </footer>
    <script src="script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
