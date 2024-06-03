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

// Récupérer le numéro du prochain quizz
$query_next_quizz_id = "SELECT MAX(id_quizz) AS max_id FROM quizzs";
$result_next_quizz_id = $conn->query($query_next_quizz_id);

if ($result_next_quizz_id) {
    $row_next_quizz_id = $result_next_quizz_id->fetch_assoc();
    $next_quizz_id = $row_next_quizz_id['max_id'] + 1;
} else {
    $error = "Erreur lors de la récupération du numéro du quizz: " . $conn->error;
}

// Récupérer les questions existantes
$query_questions = "SELECT id_question, intitule_question FROM questions";
$result_questions = $conn->query($query_questions);

if ($result_questions) {
    $questions = [];
    while ($row = $result_questions->fetch_assoc()) {
        $questions[] = $row;
    }
} else {
    $error = "Erreur lors de la récupération des questions: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["nom_quizz"]) && !empty($_POST["questions"])) {
        $nom_quizz = $_POST["nom_quizz"];
        $selected_questions = $_POST["questions"];

        $query_insert_quizz = "INSERT INTO quizzs (id_quizz, nom_quizz, bool_quizz) VALUES (?, ?, 1)";
        $stmt_insert_quizz = $conn->prepare($query_insert_quizz);
        
        if ($stmt_insert_quizz) {
            $stmt_insert_quizz->bind_param("is", $next_quizz_id, $nom_quizz);
            if ($stmt_insert_quizz->execute()) {
                $stmt_update_question = $conn->prepare("UPDATE questions SET id_quizz = ? WHERE id_question = ?");
                if ($stmt_update_question) {
                    foreach ($selected_questions as $question_id) {
                        $stmt_update_question->bind_param("ii", $next_quizz_id, $question_id);
                        if (!$stmt_update_question->execute()) {
                            $error = "Erreur lors de l'association des questions au quizz: " . $stmt_update_question->error;
                        }
                    }
                    $success_message = "Le quizz a été créé avec succès.";
                } else {
                    $error = "Erreur lors de la préparation de la mise à jour des questions: " . $conn->error;
                }
            } else {
                $error = "Erreur lors de la création du quizz: " . $stmt_insert_quizz->error;
            }
        } else {
            $error = "Erreur lors de la préparation de la création du quizz: " . $conn->error;
        }
    } else {
        $error = "Veuillez remplir tous les champs et sélectionner au moins une question.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page création Quizz</title>
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
                <h1 class="col-auto align-self-center" style="margin-right:7%;">Page création Quizz</h1>
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
                        <td class="border border-black border-end border-2">Nom du Quizz</td>
                        <td class="border border-black border-end border-2"><input class="form-control border border-black border-end border-2" type="text" name="nom_quizz" placeholder="Entrer un nom de quizz" required/></td>
                    </tr>
                    <tr>
                        <td class="border border-black border-end border-2">Numéro du Quizz</td>
                        <td class="border border-black border-end border-2"><input class="form-control border border-black border-end border-2" type="text" value="<?php echo $next_quizz_id; ?>" readonly/></td>
                    </tr>
                    <tr>
                        <td class="border border-black border-end border-2">Questions disponibles</td>
                        <td class="border border-black border-end border-2">
                            <?php foreach ($questions as $question): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="questions[]" value="<?php echo $question['id_question']; ?>" id="question<?php echo $question['id_question']; ?>">
                                    <label class="form-check-label" for="question<?php echo $question['id_question']; ?>">
                                        <?php echo $question['intitule_question']; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </td>
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
