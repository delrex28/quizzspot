<?php
// Vérifier si le token et le numéro de la question sont spécifiés dans les données GET et le cookie
if(!isset($_COOKIE['token']) || !isset($_GET['num_question'])) {
    header('Location: connexion.html'); // Redirection vers la page de connexion
    exit; 
}
//Vérifier si l'apprenant à déjà répondu à la question, et obtient le num de la question
// Récupérer le token actuellement stocké dans un cookie
$token = $_COOKIE['token'];

// URL de l'endpoint de l'API avec le code en argument
$apiEndpoint = "http://localhost/SiteQuizz/json_true_si_token.php?token=" . urlencode($token);

// Récupérer le contenu JSON de l'endpoint de l'API
$json = file_get_contents($apiEndpoint);
$data = json_decode($json, true);

// Vérifier la réponse de l'API
if(isset($data['repondu']) && $data['repondu'] === "true") {
    // L'apprenant a déjà répondu à cette question, il est redirigé vers la page d'attente entre questions
    header('Location: attente_question.php');
    exit;
} elseif (isset($data['repondu']) && $data['repondu'] === "false" && isset($data['num_question'])) {
    // L'apprenant n'a pas encore répondu à cette question, on continue
    
} else {
    // Erreur, redirection vers la page de connexion
    header('Location: connexion.html');
    exit;
}


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
            <button onclick="postReponse('1')">1</button>
            <button onclick="postReponse('2')">2</button>
            <button onclick="postReponse('3')">3</button>
            <button onclick="postReponse('4')">4</button>
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
        function postReponse(reponse) {
            // Récupérer le token de l'apprenant depuis le cookie
            var token = getToken();
            var num_question = getNum();
            
            // Vérifier si le token a été récupéré avec succès
            if (token) {
                // Envoyer la réponse à l'API
                $.ajax({
                    url: 'reponse.php', // URL de l'API pour soumettre la réponse
                    method: 'POST',
                    data: { token: token, reponse: reponse, num_question: num_question },
                    success: function(response) {                        
                        console.log('Réponse soumise avec succès : ' + reponse + " num question " + num_question + " " + token);
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
