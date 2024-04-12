<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'attente</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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
            $token = uniqid();
            setcookie("token", $token, time() + (43200), "/"); //Token dans cookie Valide pendant 1/2 journée
            // Appeler le script pour insérer le token dans la base de données
            echo "<script>insertToken('$token', '$selected_name')</script>";
            echo "<p>Le Quizz commencera sous peu.</p>";
            echo "<p>DEBUG :  VOTRE TOKEN EST $token";
            echo "<p>Vous n'êtes pas $selected_name ? <a href='connexion.html'>Retour</a></p>";
            #Ne pas oublier de rajouter une requete pour remettre le nom comme dispo
        } else {
            echo "<p>Vous n'êtes pas connecté. <a href='selection.php'>Sélectionnez votre nom</a></p>";
        }
        ?>
        <p id="status">En attente du début du quizz...</p>
    </div>
    <script>
        // Fonction pour vérifier le statut du quizz
        function checkQuizzStatus() {
            $.ajax({
                url: 'http://localhost/SiteQuizz/debut_quizz.json', //A terme, l'api enverra true si le quizz est marqué comme commencé dans la BDD
                method: 'GET',
                success: function(response) {
                    if (response.quizz_debut == true) {
                        $('#status').text('Le quizz a commencé !');
                        // Rediriger l'utilisateur vers la page du quizz
                        window.location.href = 'question.php';
                    } else {
                        $('#status').text('En attente du début du quizz...');
                    }
                },  
            });
        }
        

        setInterval(checkQuizzStatus, 5000); // Vérifier toutes les 5 secondes


        // Fonction pour insérer le token dans la base de données via l'API
        function insertToken(token) {
            $.ajax({
                url: 'insert_token.php', // URL de l'API pour l'insertion du token
                method: 'POST',
                data: { token: token, nomcomplet: nomcomplet },
                success: function(response) {
                    console.log('Token inséré avec succès dans la base de données.');
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de l\'insertion du token dans la base de données : ' + error);
                }
            });
        }
    </script>
</body>
</html>
