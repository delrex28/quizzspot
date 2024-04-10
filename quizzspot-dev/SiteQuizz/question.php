<?php
// Vérifier si le token et le numéro de la question sont spécifiés dans les données GET et le cookie
if(!isset($_COOKIE['token']) || !isset($_GET['num_question'])) {
    header('Location: connexion.html'); // Redirection vers la page de connexion
    exit; 
}

// Continuer le reste du script si les données GET sont présentes
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Répondre à la question</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
<body>
    <div class="container">
        <h1>Répondre à la question</h1>
        <div class="buttons">
            <button onclick="submitAnswer('1')">1</button>
            <button onclick="submitAnswer('2')">2</button>
            <button onclick="submitAnswer('3')">3</button>
            <button onclick="submitAnswer('4')">4</button>
        </div>
        <?php
        // Afficher le token de l'utilisateur à des fins de débogage
        $token = $_COOKIE['token'];
        echo "<p>DEBUG :  VOTRE TOKEN EST $token";
        ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>

        // Fonction pour récupérer le token à partir du cookie
        function getToken() {
            return "<?php echo isset($_COOKIE['token']) ? $_COOKIE['token'] : ''; ?>";
        }

        // Fonction pour récupérer le numéro de la question à partir des données GET
        function getNum() {
            return "<?php echo isset($_GET['num_question']) ? $_GET['num_question'] : ''; ?>";
        }

        // Fonction pour soumettre la réponse à la question
        function submitAnswer(answer) {
            // Récupérer le token de l'apprenant depuis le cookie
            var token = getToken();
            var num_question = getNum();
            
            // Vérifier si le token a été récupéré avec succès
            if (token) {
                // Envoyer la réponse à l'API
                $.ajax({
                    url: 'reponse.php', // URL de l'API pour soumettre la réponse
                    method: 'POST',
                    data: { token: token, answer: answer, num_question: num_question },
                    success: function(response) {                        
                        console.log('Réponse soumise avec succès : ' + answer + " num question " + num_question + " " + token);
                        // Rediriger l'utilisateur vers la page d'attente de la prochaine question
                        window.location.href = 'attente_question.php?num_question=' + encodeURIComponent(num_question);
                    },
                    error: function(xhr, status, error) {
                        // Gérer les erreurs en cas d'échec de la requête AJAX
                        console.error('Erreur lors de la soumission de la réponse : ' + error);
                    }
                });
            } else {
                console.error('Impossible de récupérer le token de l\'apprenant.');
            }
        }

    </script>
</body>
</html>
