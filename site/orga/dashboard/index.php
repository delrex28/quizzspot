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
                    header("Location: accueil.php");
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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        #retour {
            cursor: pointer;
        }
    </style>
</head>
<body>
  <div class="container-fluid">
      <div class="row m-4">
        <img id="retour" class="col-auto" src="img/retour.png" alt="Retour" style="width:5%;" onclick="retour()">
        <div class="col row justify-content-center">
            <h1 class="col-auto align-self-center" style="margin-right:7%;">Page connexion</h1>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php if(isset($error)) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>
            <form method="post">
                <div class="row justify-content-center mt-5">
                    <table class="col-4 table table-striped">
                        <tr class="border border-black border-2"><td class="border border-black border-end border-2 text-center">Email</td><td><input type="email" class="form-control" name="email" placeholder="Entrez votre email"></td></tr>
                        <tr class="border border-black border-2"><td class="border border-black border-end border-2 text-center">Mot de passe</td><td><input type="password" class="form-control" name="password" placeholder="Entrez votre mot de passe"></td></tr>
                    </table>
                </div>
                <div class="row justify-content-center">
                    <button type="submit" class="col-auto btn btn-lg btn-success border border-black border-2">Se Connecter</button>
                </div>
            </form>
        </div>
    </div>
  </div>
    <script>
        function retour(){
            window.location.href = "../index.html";
        }
    </script>
</body>
</html>
