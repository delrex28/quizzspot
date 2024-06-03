<?php
session_start();

include '../../query.php';
$conn = db_connect();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: ../index.php"); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

$query = "SELECT q.id_quizz, q.nom_quizz, q.bool_quizz, 
          (SELECT COUNT(*) FROM questions WHERE id_quizz = q.id_quizz) AS nb_questions,
          (SELECT valeur_moda_quizz FROM modalites_quizz WHERE id_quizz = q.id_quizz AND nom_mode_quizz = 'Temps Limité') AS temps_limite,
          (SELECT valeur_moda_quizz FROM modalites_quizz WHERE id_quizz = q.id_quizz AND nom_mode_quizz = 'Temps par Question') AS temps_par_question
          FROM quizzs q";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page administrateur paramètres Quizzs</title>
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
            <img id="retour" class="col-auto" src="../../img/retour.png" alt="Retour" style="width:5%;" onclick="retour()">
            <div class="col row justify-content-center">
                <h1 class="col-auto align-self-center" style="margin-right:7%;">Page administrateur paramètres Quizzs</h1>
            </div>
        </div>
        <div class="row justify-content-center mt-5">
            <button id="create" type="button" class="col-auto btn btn-success p-1 border border-black border-2">Créer Quizz</button>
        </div>
        <div class="row row-cols-auto justify-content-center">
        <?php
        // Vérifier s'il y a des quizzs
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id_quizz = $row["id_quizz"];
                $nom_quizz = $row["nom_quizz"];
                $statut = $row["bool_quizz"];
                $nb_questions = $row["nb_questions"];
                $temps_limite = $row["temps_limite"];
                $temps_par_question = $row["temps_par_question"];
        ?>
                <div class="col">
                    <div class="row mt-5">
                        <div class="col-auto m-4 bg-secondary-subtle rounded-5 border border-black border-2">
                            <div class="row">
                                <div class="col">
                                    <!-- Bouton Modifier -->
                                    <button type="button" class="col-auto btn btn-info p-1 border border-black border-2" onclick="modifier(<?php echo $id_quizz; ?>)">Modifier</button>
                                </div>
                                <div class="col align-self-center">
                                    <h5 class="text-primary fs-6">Quizz: <?php echo $nom_quizz; ?></h5>
                                    <p>Nombre de questions: <?php echo $nb_questions; ?></p>
                                    <p>Temps limité: <?php echo $temps_limite ? $temps_limite : 'N/A'; ?></p>
                                    <p>Temps par question: <?php echo $temps_par_question ? $temps_par_question : 'N/A'; ?></p>
                                </div>
                                <div class="col">
                                    <?php if ($statut == 1): ?>
                                        <button type="button" class="col-auto btn btn-danger p-1 border border-black border-2" onclick="toggleQuizz(<?php echo $id_quizz; ?>, 0)">Désactiver</button>
                                    <?php else: ?>
                                        <button type="button" class="col-auto btn btn-success p-1 border border-black border-2" onclick="toggleQuizz(<?php echo $id_quizz; ?>, 1)">Activer</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<p>Aucun quizz trouvé.</p>";
        }
        ?>
        </div>
    </div>

    <script>
        function retour() {
            window.location.href = '../index.php';
        }

        function modifier(id) {
            window.location.href = 'modif_quizz.php?id=' + id;
        }

        function toggleQuizz(id_quizz, new_status) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "toggle_quizz.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send("id_quizz=" + id_quizz + "&new_status=" + new_status);
        }

        document.getElementById("create").onclick = function () {
            window.location.href = 'create.php';
        };
    </script>
</body>
</html>
