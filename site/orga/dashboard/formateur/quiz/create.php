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

// Récupère le numéro du prochain quizz
$query_next_quizz_id = "SELECT MAX(id_quizz) AS max_id FROM quizzs";
$result_next_quizz_id = $conn->query($query_next_quizz_id);

if ($result_next_quizz_id) {
    $row_next_quizz_id = $result_next_quizz_id->fetch_assoc();
    $next_quizz_id = $row_next_quizz_id['max_id'] + 1;
} else {
    $error = "Erreur lors de la récupération du numéro du quizz: " . $conn->error;
}

// Récupérer les catégories existantes
$query_categories = "SELECT id_categorie, nom_categorie FROM categories";
$result_categories = $conn->query($query_categories);

if ($result_categories) {
    $categories = [];
    while ($row = $result_categories->fetch_assoc()) {
        $categories[] = $row;
    }
} else {
    $error = "Erreur lors de la récupération des catégories: " . $conn->error;
}

// Récupère les questions existantes
$query_questions = "SELECT id_question, intitule_question, id_categorie FROM questions";
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
        $temps_limite = $_POST["temps_limite"];
        $selected_questions = explode(',', $_POST["questions"]);

        $query_insert_quizz = "INSERT INTO quizzs (id_quizz, nom_quizz, bool_quizz) VALUES (?, ?, 1)";
        $stmt_insert_quizz = $conn->prepare($query_insert_quizz);
        
        if ($stmt_insert_quizz) {
            $stmt_insert_quizz->bind_param("is", $next_quizz_id, $nom_quizz);
            if ($stmt_insert_quizz->execute()) {
                // Insère le temps limité dans la table 'modalites_quizz'
                $query_insert_temps = "INSERT INTO modalites_quizz (nom_moda_quizz, valeur_moda_quizz, id_quizz) VALUES ('Temps Limité', ?, ?)";
                $stmt_insert_temps = $conn->prepare($query_insert_temps);
                if ($stmt_insert_temps) {
                    $stmt_insert_temps->bind_param("si", $temps_limite, $next_quizz_id);
                    if ($stmt_insert_temps->execute()) {
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
                        $error = "Erreur lors de la création du temps limité: " . $stmt_insert_temps->error;
                    }
                } else {
                    $error = "Erreur lors de la préparation de l'insertion du temps limité: " . $conn->error;
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
    <script>
        var selectedQuestions = []; // Array to store selected questions

        function retour() {
            window.location.href = "index.php";
        }

        function updateQuestions() {
            var selectedCategory = document.getElementById('categorie').value;
            var questionsDiv = document.getElementById('questions');
            questionsDiv.innerHTML = '';

            var questions = <?php echo json_encode($questions); ?>;
            questions.forEach(function(question) {
                if (question.id_categorie == selectedCategory) {
                    var checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = 'question_checkboxes';
                    checkbox.value = question.id_question;
                    checkbox.id = 'question' + question.id_question;
                    checkbox.classList.add('form-check-input');

                    var label = document.createElement('label');
                    label.htmlFor = checkbox.id;
                    label.textContent = question.intitule_question;
                    label.classList.add('form-check-label', 'ms-2');

                    var div = document.createElement('div');
                    div.className = 'form-check';
                    div.appendChild(checkbox);
                    div.appendChild(label);

                    if (selectedQuestions.includes(question.id_question)) {
                        checkbox.checked = true; // Check the checkbox if the question is already selected
                    }

                    questionsDiv.appendChild(div);
                }
            });
        }

        // Add event listener to update selected questions array when checkbox state changes
        document.addEventListener('change', function(event) {
            if (event.target && event.target.type === 'checkbox' && event.target.name === 'question_checkboxes') {
                var questionId = parseInt(event.target.value);
                var selectedQuestionsDiv = document.getElementById('selected-questions');
                if (event.target.checked && !selectedQuestions.includes(questionId)) {
                    selectedQuestions.push(questionId); // Add question to the array if checkbox is checked

                    // Add to the selected questions list
                    var selectedQuestionLabel = document.createElement('label');
                    selectedQuestionLabel.textContent = event.target.nextElementSibling.textContent;
                    selectedQuestionLabel.id = 'selected-question' + questionId;
                    selectedQuestionLabel.classList.add('form-check-label', 'ms-2');

                    var selectedQuestionDiv = document.createElement('div');
                    selectedQuestionDiv.className = 'form-check';
                    selectedQuestionDiv.appendChild(selectedQuestionLabel);
                    selectedQuestionsDiv.appendChild(selectedQuestionDiv);
                } else if (!event.target.checked && selectedQuestions.includes(questionId)) {
                    var index = selectedQuestions.indexOf(questionId);
                    if (index !== -1) {
                        selectedQuestions.splice(index, 1); // Remove question from the array if checkbox is unchecked

                        // Remove from the selected questions list
                        var selectedQuestionLabelToRemove = document.getElementById('selected-question' + questionId);
                        if (selectedQuestionLabelToRemove) {
                            selectedQuestionLabelToRemove.parentNode.remove();
                        }
                    }
                }
                // Update the hidden input with the selected question IDs
                document.getElementById('selected_questions_input').value = selectedQuestions.join(',');
            }
        });
    </script>
</head>
<body>
    <div class="container-fluid">
        <div class="row m-4">
            <img id="retour" class="col-auto" src="../../img/retour.png" alt="Retour" style="width:5%;" onclick="retour()">
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
                        <td class="border border-black border-2 p-3">Nom du quizz</td>
                        <td class="border border-black border-2 p-3">
                            <input class="form-control" type="text" name="nom_quizz" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black border-2 p-3">Temps limite (en secondes)</td>
                        <td class="border border-black border-2 p-3">
                            <input class="form-control" type="number" name="temps_limite" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black border-2 p-3">Catégorie</td>
                        <td class="border border-black border-2 p-3">
                            <select class="form-select" id="categorie" name="categorie" onchange="updateQuestions()" required>
                                <option value="" disabled selected>Choisir une catégorie</option>
                                <?php foreach ($categories as $categorie) : ?>
                                    <option value="<?php echo $categorie['id_categorie']; ?>"><?php echo $categorie['nom_categorie']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black border-2 p-3">Questions</td>
                        <td class="border border-black border-2 p-3" id="questions"></td>
                    </tr>
                    <tr>
                        <td class="border border-black border-2 p-3">Questions sélectionnées</td>
                        <td class="border border-black border-2 p-3" id="selected-questions"></td>
                    </tr>
                </table>
                <!-- Hidden input to store selected question IDs -->
                <input type="hidden" id="selected_questions_input" name="questions" value="">
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Créer le Quizz</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
