<?php
session_start();
include '../query.php';

if (!isset($_SESSION["user"])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id_user'])) {
    header("Location: selection_groupe.php");
    exit();
}

$id_user = intval($_GET['id_user']);

function get_quiz_by_user($id_user) {
    $conn = db_connect();
    $query = "
        SELECT q.id_quizz, q.nom_quizz 
        FROM quizzs q
        JOIN reponses_apprenant ra ON q.id_quizz = ra.id_quizz
        WHERE ra.id_user = $id_user
        GROUP BY q.nom_quizz";
    $result = $conn->query($query);

    if ($result === false) {
        echo "Erreur lors de la récupération des quiz: " . $conn->error;
        exit();
    }

    $quiz = [];
    while ($row = $result->fetch_assoc()) {
        $quiz[] = $row;
    }

    $conn->close();
    return $quiz;
}

$quiz = get_quiz_by_user($id_user);
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
            <h2 class="text-center">Sélectionner le quiz sur lequel voir les résultats :</h2>
        </div>
        <div class="row justify-content-center mt-4">
            <?php foreach ($quiz as $q) { ?>
                <div class="col-md-2 mb-2 mb-5">
                    <a href="resultats.php?id_user=<?php echo $id_user; ?>&id_quizz=<?php echo $q['id_quizz']; ?>" class="text-decoration-none">
                        <div class="card text-center border border-black border-2">
                            <div class="card-body bg-success text-white">
                                <?php echo $q['nom_quizz']; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <script>
        function retour() {
            window.location.href = "selection_apprenant.php?id_groupe=<?php echo $_GET['id_groupe']; ?>";
        }
    </script>
</body>
</html>