<?php
session_start();
include 'query.php'; // Inclure le fichier contenant la fonction db_connect()

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"]) && isset($_POST["password"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
        $email = $_POST["email"];
        $password = sha1($_POST["password"]); // Hash du mot de passe avec SHA-1

        $query = "
            SELECT utilisateurs.*, roles.nom_role 
            FROM utilisateurs 
            JOIN roles ON utilisateurs.role_user = roles.id_role 
            WHERE email_user = ?
        ";
        $user = verify_credentials($query, "s", $email);

        if ($user && $password == $user['mdp_user']) {
            if ($user['nom_role'] === 'Administrateur' || $user['nom_role'] === 'Formateur') {
                $_SESSION["user"] = $user; // Stocke les informations de l'utilisateur dans la session
                header("Location: accueil.php");
                exit();
            } elseif ($user['nom_role'] === 'Apprenant') {
                $error = "Vous n'avez pas accès à cette page.";
            } else {
                $error = "Rôle utilisateur non reconnu.";
            }
        } else {
            $error = "Identifiants incorrects. Veuillez réessayer.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

function verify_credentials($query, ...$params) {
    $conn = db_connect(); // Appelle la fonction pour établir la connexion à la base de données
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Erreur de préparation de la requête: " . $conn->error);
    }
    if ($stmt->bind_param(...$params) === false) {
        die("Erreur lors de la liaison des paramètres: " . $stmt->error);
    }
    if ($stmt->execute() === false) {
        die("Erreur lors de l'exécution de la requête: " . $stmt->error);
    }
    $result = $stmt->get_result();
    if ($result === false) {
        die("Erreur lors de la récupération des résultats: " . $stmt->error);
    }
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $user;
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
