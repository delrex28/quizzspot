<?php
session_start();
include '../../query.php';
$conn = db_connect();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: ../index.php"); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

// Vérifie si l'ID de la catégorie est passé dans l'URL
if (!isset($_GET['id'])) {
    header("Location: ../index.php"); // Redirige vers index.php si l'ID n'est pas fourni
    exit();
}

// Récupère l'ID de la catégorie depuis l'URL
$id_categorie = $_GET['id'];

// Récupère les informations de la catégorie depuis la base de données en utilisant l'ID
$query = "SELECT nom_categorie FROM categories WHERE id_categorie = $id_categorie";
$result = $conn->query($query);

// Vérifie si la catégorie existe dans la base de données
if ($result->num_rows == 0) {
    header("Location: ../index.php"); // Redirige vers index.php si la catégorie n'existe pas
    exit();
}

// Récupère les informations de la catégorie
$categorie_info = $result->fetch_assoc();
$nom_categorie = $categorie_info['nom_categorie'];

// Vérifie si le formulaire a été soumis pour la mise à jour des informations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les nouvelles valeurs du formulaire
    $nouveau_nom_categorie = $_POST['nom_categorie'];

    // Met à jour les informations de la catégorie dans la base de données
    $update_query = "UPDATE categories SET nom_categorie = ? WHERE id_categorie = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $nouveau_nom_categorie, $id_categorie);
    $stmt->execute();

    // Redirige vers la page index.php après la modification
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Paramètres Catégorie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script>
        function retour(){
            window.location.href = "index.php";
        }
    </script>
    <style>
        #retour {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row m-4">
            <img id="retour" class="col-auto" src="../../img/retour.png" alt="Retour" style="width:5%;" onclick="retour()">
            <div class="col row justify-content-center">
                <h1 class="col-auto align-self-center" style="margin-right:7%;">Page Modification Catégorie</h1>
            </div>
        </div>
    </div>
    <div class="mt-5 container">
        <div class="row justify-content-center">
            <form action="" method="post" class="col-auto">
                <table class="border border-black border-2 table table-striped fs-4">
                    <tr>
                        <td class="border-end border-black">Nom de la Catégorie</td>
                        <td><input class="form-control" type="text" id="nom_categorie" name="nom_categorie" value="<?php echo htmlspecialchars($nom_categorie); ?>" required/></td>
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
