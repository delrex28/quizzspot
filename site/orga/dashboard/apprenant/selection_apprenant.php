<?php
session_start();
include '../query.php';

if (!isset($_SESSION["user"])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id_groupe'])) {
    header("Location: selection_groupe.php");
    exit();
}

$id_groupe = intval($_GET['id_groupe']);

function get_apprenants($id_groupe) {
    $conn = db_connect();
    $query = "
        SELECT u.id_user, u.nom_user, u.prenom_user 
        FROM utilisateurs u
        JOIN rel_utilisateurs_groupes rug ON u.id_user = rug.id_user
        WHERE rug.id_groupe = $id_groupe AND u.role_user = 1"; // Modification du rôle utilisateur ici
    $result = $conn->query($query);

    if ($result === false) {
        echo "Erreur lors de la récupération des apprenants: " . $conn->error;
        exit;
    }

    $apprenants = [];
    while ($row = $result->fetch_assoc()) {
        $apprenants[] = $row;
    }

    $conn->close();
    return $apprenants;
}

$apprenants = get_apprenants($id_groupe);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Sélection Résultats Apprenant</title>
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
                <h1 class="col-auto align-self-center" style="margin-right:7%;">Page Sélection Résultats Apprenant</h1>
            </div>
        </div>
        <div class="row justify-content-center">
            <h2 class="text-center">Sélectionner l'apprenant :</h2>
        </div>
        <div class="row justify-content-center mt-4">
            <?php foreach ($apprenants as $apprenant) { ?>
                <div class="col-md-2 mb-2 mb-5">
                    <a href="selection_quizz.php?id_user=<?php echo $apprenant['id_user']; ?>" class="text-decoration-none">
                        <div class="card text-center border border-black border-2">
                            <div class="card-body bg-success text-white">
                                <?php echo $apprenant['prenom_user'] . ' ' . $apprenant['nom_user']; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <script>
        function retour() {
            window.location.href = "index.php";
        }
    </script>
</body>
</html>