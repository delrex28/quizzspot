<?php
// session_start();

// // Vérifier si l'utilisateur est connecté
// if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
//     header("Location: ../index.php"); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
//     exit();
// }

// Lire les données à partir du fichier JSON
$data = file_get_contents('bilan.json');

// Vérifier si la lecture du fichier a réussi
if ($data === false) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des données</div>";
} else {
    $results = json_decode($data, true);

    // Vérifier si les données sont valides
    if ($results === null) {
        echo "<div class='alert alert-danger'>Les données ne sont pas valides</div>";
    } else {
        // Calculer les moyennes par catégorie
        $categories = [];
        foreach ($results as $result) {
            $categorie = $result['categorie'];
            if (!isset($categories[$categorie])) {
                $categories[$categorie] = ['total' => 0, 'count' => 0, 'details' => []];
            }
            $categories[$categorie]['total'] += (int) str_replace('/20', '', $result['note']);
            $categories[$categorie]['count']++;
            $categories[$categorie]['details'][] = $result;
        }
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
    <title>QuizzSpot - Bilan</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link rel='stylesheet' type='text/css' media='screen' href='css/styles.css'>
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Résultats</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="bilan.php">Bilan</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Page content-->
    <div class="container">
        <div class="text-center mt-5">
            <h2>Bilan des résultats par catégorie</h2>
        </div>
        <?php if ($results !== null): ?>
        <table class="table mt-5">
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Moyenne des résultats</th>
                    <th>Détails</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $categorie => $data): ?>
                <tr>
                    <td><?php echo $categorie; ?></td>
                    <td><?php echo number_format($data['total'] / $data['count'], 2); ?>/20</td>
                    <td>
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse"
                            data-bs-target="#details-<?php echo md5($categorie); ?>" aria-expanded="false"
                            aria-controls="details-<?php echo md5($categorie); ?>">
                            Voir les détails
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="collapse" id="details-<?php echo md5($categorie); ?>">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nom du quiz</th>
                                        <th>Date</th>
                                        <th>Nom du formateur</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['details'] as $detail): ?>
                                    <tr>
                                        <td><?php echo $detail['nom_quizz']; ?></td>
                                        <td><?php echo $detail['date']; ?></td>
                                        <td><?php echo $detail['Nom_formateur']; ?></td>
                                        <td><?php echo $detail['note']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class='alert alert-danger'>Aucun résultat disponible</div>
        <?php endif; ?>
    </div>

</body>

</html>
