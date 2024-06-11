<?php
// session_start();

// // Vérifier si l'utilisateur est connecté
// if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
//     header("Location: ../index.php"); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
//     exit();
// }

// Récupérer l'ID de l'utilisateur
$userid = $_SESSION['userid'];

// Lire les données à partir du fichier JSON
$data = file_get_contents('results.json');

// Vérifier si la lecture du fichier a réussi
if ($data === false) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des données</div>";
} else {
    $results = json_decode($data, true);

    // Vérifier si les données sont valides
    if ($results === null) {
        echo "<div class='alert alert-danger'>Les données ne sont pas valides</div>";
    } else {
        // Stocker les résultats dans une variable PHP pour les utiliser dans le script JavaScript
        $json_results = json_encode($results);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Bilan</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link rel='stylesheet' type='text/css' media='screen' href='css/styles.css'>
    <style>
        .border {
            border: 1px solid #ccc;
        }

        .rounded {
            border-radius: 5px;
        }

        .p-3 {
            padding: 1rem !important;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .details {
            display: none;
            margin-top: 1rem;
            padding-left: 1rem;
        }
    </style>
</head>

<body>
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="https://www.quizzspot.fr">QuizzSpot</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Résultats</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="bilan.php">Bilan</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Page content-->
    <div class="container">
        <div class="text-center mt-5">
            <h2>Bienvenue sur le site de consultation des résultats de Quizzspot</h2>
        </div>

        <!-- Affichage des résultats -->
        <div class='container mt-5' id="results">
            <?php
            if ($results !== null) {
                foreach ($results as $index => $result) {
                    echo "<div class='row border rounded p-3 mb-3 result' data-index='$index'>
                            <div class='col'>{$result['nom_quizz']}</div>
                            <div class='col'>{$result['date']}</div>
                            <div class='col'>{$result['Nom_formateur']}</div>
                            <div class='col'>{$result['Note']}</div>
                          </div>
                          <div class='details' id='details-$index'></div>";
                }
            }
            ?>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Ajouter un événement de clic à chaque résultat
            $(".result").click(function () {
                const index = $(this).data("index");
                const detailsDiv = $("#details-" + index);

                // Toggle l'affichage des détails
                if (detailsDiv.is(":visible")) {
                    detailsDiv.slideUp();
                } else {
                    // Charger les détails à partir du fichier JSON
                    $.getJSON("details.json", function (data) {
                        const resultDetails = data[index];
                        if (resultDetails) {
                            detailsDiv.html("");
                            resultDetails.forEach(detail => {
                                detailsDiv.append(
                                    `<div class='row border rounded p-3 mb-3'>
                                        <div class='col'><strong>Question:</strong> ${detail.question}</div>
                                        <div class='col'><strong>Réponse donnée:</strong> ${detail.reponse_apprenant}</div>
                                        <div class='col'><strong>Réponse correcte:</strong> ${detail.reponse_correcte}</div>
                                        <div class='col'><strong>Note:</strong> ${detail.note}</div>
                                        <div class='col'><strong>Catégorie:</strong> ${detail.categorie}</div>
                                    </div>`
                                );
                            });
                            detailsDiv.slideDown();
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
