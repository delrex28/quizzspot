<?php
session_start();
include '../query.php';

if (!isset($_SESSION["user"])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id_user']) || !isset($_GET['id_quizz'])) {
    header("Location: selection_groupe.php");
    exit();
}

$id_user = intval($_GET['id_user']);
$id_quizz = intval($_GET['id_quizz']);

function get_quiz_name($id_quizz) {
    $conn = db_connect();
    $query = "SELECT nom_quizz FROM quizzs WHERE id_quizz = $id_quizz";
    $result = $conn->query($query);

    if ($result === false) {
        echo "Erreur lors de la récupération du nom du quiz: " . $conn->error;
        exit();
    }

    $row = $result->fetch_assoc();
    $quiz_name = $row['nom_quizz'];

    $conn->close();
    return $quiz_name;
}

$quiz_name = get_quiz_name($id_quizz);

function get_quiz_results($id_user, $id_quizz) {
    $conn = db_connect();
    $query = "
        SELECT q.intitule_question, r.contenu_reponse, 
               IF(ra.id_reponse IS NOT NULL, 'Correct', 'Incorrect') AS correct 
        FROM questions q
        LEFT JOIN reponses r ON q.id_question = r.id_question
        LEFT JOIN reponses_apprenant ra ON r.id_reponse = ra.id_reponse
        WHERE ra.id_user = $id_user AND q.id_quizz = $id_quizz";
    $result = $conn->query($query);

    if ($result === false) {
        echo "Erreur lors de la récupération des résultats du quiz: " . $conn->error;
        exit();
    }

    $results = [];
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }

    $conn->close();
    return $results;
}

$quiz_results = get_quiz_results($id_user, $id_quizz);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Résultat Quiz</title>
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
                <h1 class="col-auto align-self-center" style="margin-right:7%;"><?php echo "Résultats du Quiz"; ?></h1>
            </div>
        </div>
        <div class="row justify-content-center">
            <h2 class="text-center"><?php echo $quiz_name; ?></h2>
        </div>
        <div class="row justify-content-center mt-4">
            <div class="col-10">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Question</th>
                            <th>Réponse Apprenant</th>
                            <th>Correct</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quiz_results as $result) { ?>
                            <tr>
                                <td><?php echo $result['intitule_question']; ?></td>
                                <td><?php echo $result['contenu_reponse']; ?></td>
                                <td><?php echo $result['correct'] ? 'Correct' : 'Incorrect'; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        function retour() {
            window.location.href = "selection_quizz.php?id_user=<?php echo $_GET['id_user']; ?>";
        }
    </script>
</body>
</html>
