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
    </div>
    <?php
        function getNum() {
            // Vérifiez si le paramètre 'num_question' est défini dans l'URL
            if(isset($_GET['num_question'])) {
                // Récupérez et retournez la valeur de 'num_question'
                return $_GET['num_question'];
            } else {
                return -1; //-1 indique une valeur invalide ou non définie
            }
        }
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>

        // Fonction pour récupérer le token à partir du cookie
        function getToken() {
            return "<?php echo isset($_COOKIE['token']) ? $_COOKIE['token'] : ''; ?>";
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
                    data: { token: token, answer: answer, num_question : num_question },
                    success: function(response) {                        
                        console.log('Réponse soumise avec succès : ' + answer + " num question" +  num_question + " "+ token);
                        // Rediriger l'utilisateur vers la prochaine question ou une autre page
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
