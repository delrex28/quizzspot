<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: ../index.php"); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

include '../query.php';
$conn = db_connect();

$success_message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si les champs requis sont définis et non vides
    if (isset($_POST["prenom"]) && isset($_POST["nom"]) && isset($_POST["email"]) && isset($_POST["password"])) {
        // Récupérer les valeurs des champs
        $prenom = $_POST["prenom"];
        $nom = $_POST["nom"];
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Hacher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Requête SQL pour vérifier si l'email est déjà utilisé
        $query_email_check = "SELECT COUNT(*) AS count FROM utilisateurs WHERE email_user = ?";
        $stmt_email_check = $conn->prepare($query_email_check);
        $stmt_email_check->bind_param("s", $email);
        $stmt_email_check->execute();
        $result_email_check = $stmt_email_check->get_result();
        $row = $result_email_check->fetch_assoc();
        $email_count = $row['count'];

        if ($email_count > 0) {
            // L'email est déjà utilisé, afficher un message d'erreur
            $error = "Cette adresse e-mail est déjà utilisée. Veuillez en choisir une autre.";
        } else {
            // L'email n'est pas déjà utilisé, insérer l'utilisateur dans la base de données
            $query_insert_user = "INSERT INTO utilisateurs (prenom_user, nom_user, email_user, mdp_user, role_user) VALUES (?, ?, ?, ?, 2)";
            $stmt_insert_user = $conn->prepare($query_insert_user);
            $stmt_insert_user->bind_param("ssss", $prenom, $nom, $email, $hashed_password);

            // Exécuter la requête d'insertion
            if ($stmt_insert_user->execute()) {
                // Message de succès
                $success_message = "L'utilisateur a été créé avec succès.";
            } else {
                // Gérer les erreurs si la requête d'insertion échoue
                $error = "Une erreur s'est produite lors de la création de l'utilisateur.";
            }
        }
    } else {
        // Gérer les erreurs si des champs requis sont manquants
        $error = "Veuillez remplir tous les champs.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page création Formateur</title>
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
            <img id="retour" class="col-auto" src="../img/retour.png" alt="Retour" style="width:5%;" onclick="retour()">
            <div class="col row justify-content-center">
                <h1 class="col-auto align-self-center" style="margin-right:7%;">Page création Formateur</h1>
            </div>
        </div>
    </div>
        
    <div class="mt-5">
        <div class="row justify-content-center">
            <?php if (!empty($success_message)) : ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="" method="post" class="col-auto">
                <table class="border border-black border-2 table table-striped fs-4">
                    <tr>
                        <td class="border border-black border-end border-2">Prénom</td>
                        <td class="border border-black border-end border-2"><input class="form-control border border-black border-end border-2" type="text" name="prenom" placeholder="Entrer un prénom" required/></td>
                    </tr>
                    <tr>
                        <td class="border border-black border-end border-2">Nom</td>
                        <td class="border border-black border-end border-2"><input class="form-control border border-black border-end border-2" type="text" name="nom" placeholder="Entrer un nom" required/></td>
                    </tr>
                    <tr>
                        <td class="border border-black border-end border-2">Adresse mail</td>
                        <td class="border border-black border-end border-2"><input class="form-control border border-black border-end border-2" type="email" name="email" placeholder="Entrer une email" required/></td>
                    </tr>
                    <tr>
                        <td class="border border-black border-end border-2">Mot de passe</td>
                        <td class="border border-black border-end border-2"><input class="form-control border border-black border-end border-2" type="password" name="password" placeholder="Entrer un mot de passe" required/></td>
                    </tr>
                </table>
                <div class="row justify-content-center">
                    <button type="submit" class="border border-black border-2 col-auto btn btn-success btn-lg">Créer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function retour() {
            window.location.href = "index.php";
        }
    </script>
</body>
</html>
