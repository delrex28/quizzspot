<?php
session_start();
include '../query.php';
$conn = db_connect();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: login.php"); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

// Vérifier si l'ID de l'utilisateur est passé dans l'URL
if (!isset($_GET['id'])) {
    header("Location: ../index.php"); // Redirige vers index.php si l'ID n'est pas fourni
    exit();
}

// Récupérer l'ID de l'utilisateur depuis l'URL
$id_utilisateur = $_GET['id'];

// Récupérer les informations de l'utilisateur depuis la base de données en utilisant l'ID
$query = "SELECT prenom_user, nom_user FROM utilisateurs WHERE id_user = $id_utilisateur";
$result = $conn->query($query);

// Vérifier si l'utilisateur existe dans la base de données
if ($result->num_rows == 0) {
    header("Location: index.php"); // Redirige vers index.php si l'utilisateur n'existe pas
    exit();
}

// Récupérer les informations de l'utilisateur
$user_info = $result->fetch_assoc();
$prenom = $user_info['prenom_user'];
$nom = $user_info['nom_user'];

// Vérifier si le formulaire a été soumis pour la mise à jour des informations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les nouvelles valeurs du formulaire
    $nouveau_prenom = $_POST['prenom_user'];
    $nouveau_nom = $_POST['nom_user'];

    // Mettre à jour les informations de l'utilisateur dans la base de données
    $update_query = "UPDATE utilisateurs SET prenom_user = '$nouveau_prenom', nom_user = '$nouveau_nom' WHERE id_user = $id_utilisateur";
    $conn->query($update_query);

    // Rediriger vers la page index.php après la modification
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Paramètres Formateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script>
        function retour(){
            window.location.href = "index.php";
        }
    </script>
</head>
    <style>
        #retour {
            cursor: pointer;
        }
    </style>
<body>
    <div class="row m-4">
        <img id="retour" class="col-auto" src="../img/retour.png" alt="Retour" style="width:5%;" onclick="retour()">
        <div class="col row justify-content-center">
            <h1 class="col-auto align-self-center" style="margin-right:7%;">Page Modification Formateur</h1>
        </div>
    </div>
    <div class="mt-5 container">
        <div class="row justify-content-center">
            <form action="" method="post" class="col-auto">
                <table class="border border-black border-2 table table-striped fs-4">
                    <tr>
                        <td class="border-end border-black">Prénom</td>
                        <td><input class="form-control" type="text" id="prenom_user" name="prenom_user" value="<?php echo htmlspecialchars($prenom); ?>" required/></td>
                    </tr>
                    <tr>
                        <td class="border-end border-black">Nom</td>
                        <td><input class="form-control" type="text" id="nom_user" name="nom_user" value="<?php echo htmlspecialchars($nom); ?>" required/></td>
                    </tr>
                </table>
                <div class="row justify-content-center">
                    <button type="submit" class="col-auto btn btn-lg btn-success">Modifier</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
