<?php
session_start();
include 'query.php'; // Inclure le fichier contenant la fonction db_connect()

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"]) && isset($_POST["password"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Faire une requête pour vérifier l'email et obtenir le hash du mot de passe
        $query = "SELECT * FROM utilisateurs WHERE email_user = ?";
        $user = verify_credentials($query, "s", $email);

        if ($user) {
            // Vérification avec SHA1 (ancien hachage)
            if ($user['mdp_user'] === sha1($password)) {
                // Réhachage du mot de passe avec une méthode plus sécurisée
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                update_password_hash($user['id_user'], $newHash);
                
                $user['mdp_user'] = $newHash; // Met à jour le hash de l'utilisateur pour la session
            }

            // Vérification avec password_verify pour le nouveau hash
            if (password_verify($password, $user['mdp_user'])) {
                $role = $user['role_user'];
                if ($role === 3 || $role === 2) {
                    $_SESSION["user"] = $user;
                    header("Location: dashboard/accueil.php");
                    exit();
                } elseif ($role === 1) {
                    $error = "Les apprenants ne peuvent pas se connecter.";
                } else {
                    $error = "Rôle utilisateur non reconnu.";
                }
            } else {
                $error = "Identifiants incorrects. Veuillez réessayer.";
            }
        } else {
            $error = "Identifiants incorrects. Veuillez réessayer.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

function verify_credentials($query, ...$params) {
    $conn = db_connect();
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

function update_password_hash($user_id, $new_hash) {
    $conn = db_connect();
    $query = "UPDATE utilisateurs SET mdp_user = ? WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo "Erreur de préparation de la requête: " . $conn->error;
        exit;
    }
    if ($stmt->bind_param("si", $new_hash, $user_id) === false) {
        echo "Erreur lors de la liaison des paramètres: " . $stmt->error;
        exit;
    }
    if ($stmt->execute() === false) {
        echo "Erreur lors de l'exécution de la requête: " . $stmt->error;
        exit;
    }
    $stmt->close();
    $conn->close();
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
            <a href="https://www.quizzspot.fr">Accueil</a>
            <a href="https://orga.quizzspot.fr">Espace Orga</a>
            <!-- <a href="https://admin.quizzspot.fr">Espace Administrateur</a> -->
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
                <form method="post">
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
                        if (isset($error)) {
                            echo "<p style='color:yellow'>$error</p>";
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
