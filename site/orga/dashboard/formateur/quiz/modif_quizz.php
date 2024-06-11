<?php
session_start();
include '../../query.php';
$conn = db_connect();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: ../index.php"); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

// Vérifier si l'ID du quizz est passé dans l'URL
if (!isset($_GET['id'])) {
    header("Location: ../index.php"); // Redirige vers index.php si l'ID n'est pas fourni
    exit();
}

// Récupère l'ID du quizz depuis l'URL
$id_quizz = $_GET['id'];

// Récupère les informations du quizz depuis la base de données en utilisant l'ID
$query = "SELECT nom_quizz FROM quizzs WHERE id_quizz = $id_quizz";
$result = $conn->query($query);

// Vérifie si le quizz existe dans la base de données
if ($result->num_rows == 0) {
    header("Location: ../index.php"); // Redirige vers index.php si le quizz n'existe pas
    exit();
}

// Récupère les informations du quizz
$quizz_info = $result->fetch_assoc();
$nom_quizz = $quizz_info['nom_quizz'];

// Récupère le temps limité du quizz depuis la table modalites_quizz
$query_temps_limite = "SELECT valeur_moda_quizz FROM modalites_quizz WHERE id_quizz = $id_quizz AND nom_moda_quizz = 'Temps Limité'";
$result_temps_limite = $conn->query($query_temps_limite);

if ($result_temps_limite->num_rows > 0) {
    $temps_limite_info = $result_temps_limite->fetch_assoc();
    $temps_limite = $temps_limite_info['valeur_moda_quizz'];
} else {
    $temps_limite = ""; // Valeur par défaut si le temps limité n'existe pas
}

// Vérifie si le formulaire a été soumis pour la mise à jour des informations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les nouvelles valeurs du formulaire
    $nouveau_nom_quizz = $_POST['nom_quizz'];
    $nouveau_temps_limite = $_POST['temps_limite'];

    // Mettre à jour les informations du quizz dans la base de données
    $update_query = "UPDATE quizzs SET nom_quizz = ? WHERE id_quizz = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $nouveau_nom_quizz, $id_quizz);
    $stmt->execute();

    // Mettre à jour le temps limité dans la table modalites_quizz
    if (!empty($nouveau_temps_limite)) {
        // Vérifie si un enregistrement existe déjà pour ce quizz
        $check_query = "SELECT * FROM modalites_quizz WHERE id_quizz = ? AND nom_moda_quizz = 'Temps Limité'";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $id_quizz);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Mise à jour de l'enregistrement existant
            $update_temps_query = "UPDATE modalites_quizz SET valeur_moda_quizz = ? WHERE id_quizz = ? AND nom_mode_quizz = 'Temps Limité'";
            $stmt_temps = $conn->prepare($update_temps_query);
            $stmt_temps->bind_param("si", $nouveau_temps_limite, $id_quizz);
        } else {
            // Insertion d'un nouvel enregistrement
            $insert_temps_query = "INSERT INTO modalites_quizz (nom_moda_quizz, valeur_moda_quizz, id_quizz) VALUES ('Temps Limité', ?, ?)";
            $stmt_temps = $conn->prepare($insert_temps_query);
            $stmt_temps->bind_param("si", $nouveau_temps_limite, $id_quizz);
        }
        $stmt_temps->execute();
    }

    // Redirige vers la page index.php après la modification
	header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Paramètres Quizz</title>
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
        <img id="retour" class="col-auto" src="../../img/retour.png" alt="Retour" style="width:5%;" onclick="retour()">
        <div class="col row justify-content-center">
            <h1 class="col-auto align-self-center" style="margin-right:7%;">Page Modification Quizz</h1>
        </div>
    </div>
    <div class="mt-5 container">
        <div class="row justify-content-center">
            <form action="" method="post" class="col-auto">
                <table class="border border-black border-2 table table-striped fs-4">
                    <tr>
                        <td class="border-end border-black">Nom du Quizz</td>
                        <td><input class="form-control" type="text" id="nom_quizz" name="nom_quizz" value="<?php echo htmlspecialchars($nom_quizz); ?>" required/></td>
                    </tr>
                    <tr>
                        <td class="border-end border-black">Temps Limité (en secondes)</td>
                        <td><input class="form-control" type="number" id="temps_limite" name="temps_limite" value="<?php echo htmlspecialchars($temps_limite); ?>" required/></td>
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
