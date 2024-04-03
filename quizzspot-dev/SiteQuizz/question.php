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
            <button onclick="submitAnswer('Paris')">Paris</button>
            <button onclick="submitAnswer('Londres')">Londres</button>
            <button onclick="submitAnswer('Berlin')">Berlin</button>
            <button onclick="submitAnswer('Rome')">Rome</button>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // Fonction pour soumettre la réponse à la question
        function submitAnswer(answer) {
            $.ajax({
                url: 'submit_answer.php', // URL de l'API pour soumettre la réponse
                method: 'POST',
                data: { answer: answer },
                success: function(response) {
                    // Afficher un message ou effectuer d'autres actions en fonction de la réponse de l'API
                    console.log('Réponse soumise avec succès : ' + answer);
                    // Rediriger l'utilisateur vers la prochaine question ou une autre page
                    window.location.href = 'prochaine_question.php';
                },
                error: function(xhr, status, error) {
                    // Gérer les erreurs en cas d'échec de la requête AJAX
                    console.error('Erreur lors de la soumission de la réponse : ' + error);
                }
            });
        }
    </script>
</body>
</html>
