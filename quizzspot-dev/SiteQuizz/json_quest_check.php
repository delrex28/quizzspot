<?php

// Vérifier si le token est présent dans les données POST
if(isset($_GET['token'])) {
    // Récupérer le token depuis les données POST
    $token = $_GET['token'];

    // Générer un JSON de réponse avec le token reçu
    $response = array(
        'repondu' => 'false', //false, l'apprenant n'a pas répondu à la question
        'num_question' => '3' //renvoie le num de la question en cours
    );

    // Envoyer le JSON en tant que réponse
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Si aucun token n'est reçu, renvoyer une erreur
    header('Content-Type: application/json');
    http_response_code(400); // Code d'erreur "Bad Request"
    echo json_encode(array('repondu' => 'false'));
}

?>
