<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>QuizzSpot</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link rel='stylesheet' type='text/css' media='screen' href='css/styles.css'>
</head>

<body>
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">QuizzSpot</a>
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
        <?php
        session_start();

        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header("Location: login.php"); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            exit();
        }

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
                // Afficher les résultats
                echo "<div class='container mt-5'>";
                foreach ($results as $result) {
                    echo "<div class='row border rounded p-3 mb-3'><div class='col'>" . $result['nom_quizz'] . "</div><div class='col'>" . $result['date'] . "</div><div class='col'>" . $result['Nom_formateur'] . "</div><div class='col'>" . $result['Note'] . "</div></div>";
                }
                echo "</div>";
            }
        }
        ?>
    </div>
</body>

</html>
