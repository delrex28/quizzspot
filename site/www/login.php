<!DOCTYPE html>
<html lang="fr-FR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quizzspot</title>
        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    </head>
    <body>
        <header>
            <h2><a href="https://www.quizzspot.fr"><img src="images/icon-quizzspot.png">Quizz:Spot</a></h2>
            
            <nav>
                <a href="#">Acceuil</a>
                <a href="#">Quizz</a>
                <a href="https://orga.quizzspot.fr">Espace Orga</a>
                <a href="#">Compte</a>
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
            <div class="left">
                <h1>Pret a gagner avec <br><span>Quizz:Spot</span></h1>
                <p>Quizz:Spot, c'est une interface de quizz en tout genre pour mobile. Disponible partout même sans connexion. Commence maintenant!"<ion-icon name="rocket-outline"></ion-icon></p>
                <button>Commencer<ion-icon name="arrow-forward-outline"></ion-icon></button>
            </div>
            <div class="right">
                <h1>Se connecter</h1>
                <form action="verification.php" method="post">
                    <div class="inputbox">
                        <ion-icon name="person-outline"></ion-icon>
                        <input type="text" placeholder="Nom d'utilisateur" name="username" required>
                    </div>

                    <div class="inputbox">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" placeholder="Mot de passe" name="password" required>
                    </div>

                    <div class="forgot">
                        <div class="checkbox">
                            <input type="checkbox">
                            <label>Se rappeler de moi</label>
                        </div>
                        <a href="#">Mot de passe oublier?</a>
                    </div>

                    <button type="submit">Connexion</button>
                    <?php
                    if(isset($_GET['erreur'])){
                    $err = $_GET['erreur'];
                    if($err==1 || $err==2)
                    echo "<p style='color:yellow'>Utilisateur ou mot de passe incorrect</p>";
                    }
                    ?>

                    <div class="another">
                        <p>Je n'est pas de compte <a href="#">S'inscrire</a></p>
                    
                        <div class="btns">
                            <button><img src="images/google.png" alt="">Google</button>
                            <button><img src="images/facebook.png" alt="">Facebook</button> 
                        </div>
                    </div>
                </form>
        </section>
        <script src="script.js"></script>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    </body>
</html>