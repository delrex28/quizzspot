

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identification</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
<body>
    <div class="container" id="identif">
        <h1>Connexion au Quizz</h1>
        <form action="attente.php" method="get">
            <?php
                // Récupérer le code
                $code = isset($_GET['code']) ? $_GET['code'] : '';

                // URL de l'endpoint de l'API avec le code en argument
                $apiEndpoint = "http://localhost/SiteQuizz/json_code.php?code=" . urlencode($code);

                // Récupérer le contenu JSON de l'endpoint de l'API
                $json = file_get_contents($apiEndpoint);

                // Vérifier si la requête a réussi
                if ($json !== false) {
                    // Convertir le JSON en tableau associatif
                    $data = json_decode($json, true);

                    // Vérifier si le code est valide
                    if (isset($data['test']['code']) && $data['test']['code'] === true) {
                        // Vérifier si le JSON contient des participants
                        if (isset($data['participants'])) {
                            $participants = $data['participants'];

                            // Afficher la liste déroulante des participants
                            echo '<div class="form-group">';
                            echo '<label for="nomcomplet">Sélectionnez votre prénom et nom :</label>';
                            echo '<select name="nomcomplet">';
                            echo '<option value=""></option>';

                            // Parcourir les participants et les insérer dans la liste déroulante
                            foreach ($participants as $participant) {
                                $nomcomplet = $participant['prenom'] . " " . $participant['nom'];
                                echo "<option value='" . htmlspecialchars($nomcomplet) . "'>" . htmlspecialchars($nomcomplet) . "</option>";
                            }

                            echo '</select>';
                            echo '</div>';
                        } else {
                            // Aucun participant trouvé dans le JSON
                            echo "<p>Tout les participants inscrit sont déjà connectés</p>";
                        }
                    } else {
                        // Code invalide : Afficher un bouton pour retourner à la page précédente
                        echo '<p>Code invalide. Cliquez <a href="connexion.html">ici</a> pour retourner à la page précédente.</p>';
                    }
                } else {
                    // Erreur lors de la récupération des données JSON depuis l'API
                    echo "<p>Erreur lors de la récupération des données JSON depuis l'API.</p>";
                }
            ?>
            <?php if(isset($data['test']['code']) && $data['test']['code'] === true): ?>
                <button type="submit">Se Connecter</button>
            <?php endif; ?>
        </form>
    </div>    
</body>
</html>
