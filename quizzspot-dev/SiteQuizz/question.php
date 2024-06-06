
<?php
function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}
// Vérifier si le token est spécifié
if(!isset($_COOKIE['token'])) {
    debug_to_console("pas de token");
    header('Location: connexion.html'); // Redirection vers la page de connexion
    exit; 
}
//Vérifier si l'apprenant à déjà répondu à la question
$token = $_COOKIE['token'];
// URL de l'endpoint de l'API avec le token  en argument
$apiEndpoint = "https://app.quizzspot.fr/has_responded?token=" . urlencode($token);

// Récupérer le contenu JSON de l'endpoint de l'API
$json = file_get_contents($apiEndpoint);
$data = json_decode($json, true);

// Vérifier la réponse de l'API
if(isset($data['has_responded']) && $data['has_responded'] === "true") {
    debug_to_console("deja repondu");
    // L'apprenant a déjà répondu à cette question, il est redirigé vers la page d'attente entre questions
    header('Location: attente_question.php');
    exit;
} else {
    debug_to_console("pas repondu");
    }
    // L'apprenant n'a pas encore répondu à cette question, on continue
    
// } else {
//     // Erreur, redirection vers la page de connexion
//     debug_to_console("erreur");
//     header('Location: connexion.html');
//     exit;
// }


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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>

        // Fonction pour récupérer le token à partir du cookie
        function getToken() {
            return "<?php echo isset($_COOKIE['token']) ? $_COOKIE['token'] : ''; ?>";
        }
        // Fonction pour rediriger vers la page d'attente des questions
        function redirigerVersAttente() {
            window.location.href = 'attente_question.php';
        }
        // Fonction pour récupérer le numéro de la question et le temps imparti en cours depuis l'API
        function getNum() {
            var num_question = null;
            $.ajax({
                url: 'https://app.quizzspot.fr/current_question', // URL de l'API pour obtenir le numéro de la question en cours
                method: 'GET',
                async: true, // Utilisation de la synchronisation pour attendre la réponse de l'API
                success: function(response) {
                    num_question = response.current_question;
                    console.log("num question obtenu :" + num_question );
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de la récupération du numéro de la question : ' + error);
                }
            });
            return num_question;
        }
        //récupérer le temps imparti
        function getTemps() {
            var temps = 60; // 30s par défaut
            $.ajax({
                url: 'https://app.quizzspot.fr/current_question', // URL de l'API pour obtenir le temps de la question en cours
                method: 'GET',
                async: true, // Utilisation de la synchronisation pour attendre la réponse de l'API
                success: function(response) {
                    temps = response.temps_alloue;
                    console.log("temps obtenu :" + temps );
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de la récupération du temps de la question : ' + error);
                }
            });
            return temps;  
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
                    url: 'https://app.quizzspot.fr/submit_answer', // URL de l'API pour soumettre la réponse
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ token: token, nom_reponse: reponse}),
                    success: function(response) {                        
                        console.log('Réponse soumise avec succès : ' + reponse + " " + token);
                        // Rediriger l'utilisateur vers la page d'attente de la prochaine question
                        window.location.href = 'attente_question.php';
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
        // Définir la durée du décompte (en secondes)
        var temps_alloue = getTemps();


        // Démarre le décompte du temps imparti
        setTimeout(redirigerVersAttente, temps_alloue * 1000); // Convertit le temps en millisecondes

    </script>
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

</body>
</html>
