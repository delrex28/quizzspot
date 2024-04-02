<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'attente</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
<body>
    <div class="container">
        <h1>Bienvenue !</h1>
        <?php
        // Vérifier si un nom a été sélectionné
        if (isset($_GET['nomcomplet'])) {
            // Récupérer le nom sélectionné
            $selected_name = $_GET['nomcomplet'];
            echo "<p>Connecté en tant que : $selected_name</p>";
            echo "<p>Le Quizz commencera sous peu.</p>";
            echo "<p>Vous n'êtes pas $selected_name ? <a href='connexion.html'>Retour</a></p>";
            #Ne pas oublier de rajouter une requete pour remettre le nom comme dispo
        } else {
            echo "<p>Vous n'êtes pas connecté. <a href='selection.php'>Sélectionnez votre nom</a></p>";
        }
        ?>
    </div>
</body>
</html>
