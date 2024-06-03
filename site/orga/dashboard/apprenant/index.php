<?php
session_start();
include '../query.php'; // Inclue le fichier contenant la fonction db_connect()

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: ../index.php");
    exit();
}

// Récupère les groupes depuis la base de données
function get_groupes() {
    $conn = db_connect();
    $query = "SELECT id_groupe, nom_groupe FROM groupes WHERE bool_groupe = 1";
    $result = $conn->query($query);

    if ($result === false) {
        echo "Erreur lors de la récupération des groupes: " . $conn->error;
        exit;
    }

    $groupes = [];
    while ($row = $result->fetch_assoc()) {
        $groupes[] = $row;
    }

    $conn->close();
    return $groupes;
}

$groupes = get_groupes();
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
            <h2 class="text-center">Sélectionner le Groupe auquel appartient l'apprenant :</h2>
        </div>
        <div class="row justify-content-center mt-4">
            <?php foreach ($groupes as $groupe) { ?>
                <div class="col-md-2 mb-2 mb-5">
                    <a href="selection_apprenant.php?id_groupe=<?php echo $groupe['id_groupe']; ?>" class="text-decoration-none">
                        <div class="card text-center border border-black border-2">
                            <div class="card-body bg-success text-white">
                                <?php echo $groupe['nom_groupe']; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <script>
        function retour() {
            window.location.href = "../accueil.php";
        }
    </script>
</body>
</html>
