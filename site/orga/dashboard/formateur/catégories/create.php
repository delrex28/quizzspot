<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: ../index.php"); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

include '../../query.php';
$conn = db_connect();

if (!$conn) {
    die("Échec de la connexion à la base de données: " . mysqli_connect_error());
}

$success_message = "";
$error = "";

// Récupère le numéro de la prochaine catégorie
$query_next_categorie_id = "SELECT MAX(id_categorie) AS max_id FROM categories";
$result_next_categorie_id = $conn->query($query_next_categorie_id);

if ($result_next_categorie_id) {
    $row_next_categorie_id = $result_next_categorie_id->fetch_assoc();
    $next_categorie_id = $row_next_categorie_id['max_id'] + 1;
} else {
    $error = "Erreur lors de la récupération du numéro de la catégorie: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["nom_categorie"])) {
        $nom_categorie = $_POST["nom_categorie"];

        $query_insert_categorie = "INSERT INTO categories (id_categorie, nom_categorie, bool_categorie) VALUES (?, ?, 1)";
        $stmt_insert_categorie = $conn->prepare($query_insert_categorie);
        
        if ($stmt_insert_categorie) {
            $stmt_insert_categorie->bind_param("is", $next_categorie_id, $nom_categorie);
            if ($stmt_insert_categorie->execute()) {
                $success_message = "La catégorie a été créée avec succès.";
            } else {
                $error = "Erreur lors de la création de la catégorie: " . $stmt_insert_categorie->error;
            }
        } else {
            $error = "Erreur lors de la préparation de la création de la catégorie: " . $conn->error;
        }
    } else {
        $error = "Veuillez entrer un nom de catégorie.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page création Catégorie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        #retour {
            cursor: pointer;
        }
    </style>
    <script>
        function retour() {
            window.location.href = "index.php";
        }
    </script>
</head>
<body>
    <div class="container-fluid">
        <div class="row m-4">
            <img id="retour" class="col-auto" src="../../img/retour.png" alt="Retour" style="width:5%;" onclick="retour()">
            <div class="col row justify-content-center">
                <h1 class="col-auto align-self-center" style="margin-right:7%;">Page création Catégorie</h1>
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
                        <td class="border border-black border-end border-2">Nom de la Catégorie</td>
                        <td class="border border-black border-end border-2"><input class="form-control border border-black border-end border-2" type="text" name="nom_categorie" placeholder="Entrer un nom de catégorie" required/></td>
                    </tr>
                    <tr>
                        <td class="border border-black border-end border-2">Numéro de la Catégorie</td>
                        <td class="border border-black border-end border-2"><input class="form-control border border-black border-end border-2" type="text" value="<?php echo $next_categorie_id; ?>" readonly/></td>
                    </tr>
                </table>
                <div class="row justify-content-center">
                    <button type="submit" class="border border-black border-2 col-auto btn btn-success btn-lg">Créer</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
