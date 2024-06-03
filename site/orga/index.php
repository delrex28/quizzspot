<?php
session_start();
include 'query.php'; // Inclure le fichier contenant la fonction db_connect()

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifie si les champs email et mot de passe sont définis et non vides
    if (isset($_POST["email"]) && isset($_POST["password"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
        // Récupère les valeurs des champs
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Faire une requête pour vérifier l'email et obtenir le hash du mot de passe
        $query = "SELECT * FROM utilisateurs WHERE email_user = ?";
        $user = verify_credentials($query, "s", $email);

        // Si un utilisateur correspondant est trouvé, vérifier le mot de passe
        if ($user && password_verify($password, $user['mdp_user'])) {
            // Vérifie le rôle de l'utilisateur
            if ($user['role_user'] === 'administrateur') {
                $_SESSION["user"] = $user; // Stocke les informations de l'utilisateur dans la session
                header("Location: accueil.php");
                exit();
            } elseif ($user['role_user'] === 'formateur') {
                $_SESSION["user"] = $user; // Stocke les informations de l'utilisateur dans la session
                header("Location: accueil.php");
                exit();
            } elseif ($user['role_user'] === 'apprenant') {
                $error = "Vous n'avez pas accès à cette page.";
            } else {
                $error = "Rôle utilisateur non reconnu.";
            }
        } else {
            $error = "Identifiants incorrects. Veuillez réessayer."; // Message d'erreur si les identifiants sont incorrects
        }
    } else {
        $error = "Veuillez remplir tous les champs."; // Message d'erreur si des champs sont manquants
    }
}

function verify_credentials($query, ...$params) {
    $conn = db_connect(); // Appelle la fonction pour établir la connexion à la base de données
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo "Erreur de préparation de la requête: " . $conn->error;
        exit;
    }
    if ($stmt->bind_param(...$params) === false) {
        echo "Erreur lors de la liaison des paramètres: " . $stmt->error;
        exit;
    }
    if ($stmt->execute() === false) {
        echo "Erreur lors de l'exécution de la requête: " . $stmt->error;
        exit;
    }
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $user;
}
?>

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
                <a href="https://www.quizzspot.fr">Acceuil</a>
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
                            <input type="email" name="username" required>
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
                                <a href="#">Mot de passe Oublier?</a>
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
                <li><a href="#"></a>Accueil</li>
                <li><a href="#"></a>A propos</li>
                <li><a href="#"></a>L'équipe</li>
                <li><a href="#"></a>Nous contacter</li>
            </ul>
            <p>@2024 Quizzspot | Tout droit Résérver</p>
        </footer>
        <script src="script.js"></script>
        <script src="script.js"></script>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    </body>
</html>

