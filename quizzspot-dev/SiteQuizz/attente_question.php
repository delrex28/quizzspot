<?php
// Vérifier si l'utilisateur a un token
if(!isset($_COOKIE['token'])) {
    header('Location: connexion.html'); // Redirection vers la page de connexion
    exit; 
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attente de la prochaine question</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
<body>
    <div class="container">
        <h3>Votre réponse à été enregistrée</h3>
        <h2>En attente de la prochaine question...</h2>
    </div>
</body>
</html>
