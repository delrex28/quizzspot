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
        <form id="loginForm" action="attente.php" method="get">
            <?php

            function debug_to_console($data)
            {
                $output = $data;
                if (is_array($output))
                    $output = implode(',', $output);

                echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
            }
            // Récupérer le code
            $code = isset($_GET['code']) ? $_GET['code'] : '';

            // URL de l'endpoint de l'API avec le code en argument
            $apiEndpoint = "https://app.quizzspot.fr/validate_code?code=" . urlencode($code);
            // $apiEndpoint = "http://localhost/SiteQuizz/json_code.php?code=" .urlencode($code);
            // Récupérer le contenu JSON de l'endpoint de l'API
            $json = file_get_contents($apiEndpoint);

            // Vérifier si la requête a réussi
            if ($json !== false) {
                // Convertir le JSON en tableau associatif
                $data = json_decode($json, true);

                // Vérifier si le code est valide
                if (isset($data['test']['code']) && $data['test']['code'] === true) {
                    debug_to_console("code valide");
                    // Vérifier si le JSON contient des participants
                    if (isset($data['participants'])) {
                        $participants = $data['participants'];

                        // Afficher la liste déroulante des participants
                        echo '<div class="form-group">';
                        echo '<label for="nomcomplet">Sélectionnez votre prénom et nom :</label>';
                        echo '<select name="nomcomplet" id="nomcomplet" required>';
                        echo '<option value=""></option>';

                        // Parcourir les participants et les insérer dans la liste déroulante
                        foreach ($participants as $participant) {
                            $nomcomplet = $participant['prenom_user'] . " " . $participant['nom_user'];
                            echo "<option value='" . htmlspecialchars($nomcomplet) . "'>" . htmlspecialchars($nomcomplet) . "</option>";
                        }

                        echo '</select>';
                        echo '</div>';
                    } else {
                        // Aucun participant trouvé dans le JSON
                        echo "<p>Aucun participants acquis</p>";
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
            <?php if (isset($data['test']['code']) && $data['test']['code'] === true): ?>
                <button type="submit">Se Connecter</button>
            <?php endif; ?>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        $(document).ready(function () {
            // Lorsqu'un nom est sélectionné dans la liste
            $('#nomcomplet').on('change', function () {
                var selectedName = $(this).val();

                // Séparer la chaîne en utilisant l'espace comme délimiteur
                let nameParts = selectedName.split(" ");

                // Assigner les parties aux variables prénom et nom
                let prenom = nameParts[0];
                let nom = nameParts[1];

                if (!prenom || !nom) {
                    console.error('Prénom ou nom manquant.');
                    return;
                }

                console.log("Prénom:", prenom); // Affiche: Prénom
                console.log("Nom:", nom);       // Affiche: Nom

                // (Ré)activer le bouton pour permettre la soumission du formulaire
                $('button[type="submit"]').prop('disabled', false);
            });

            // Lorsque le bouton de soumission est cliqué
            $('button[type="submit"]').on('click', function (e) {
                e.preventDefault(); // Empêche la soumission du formulaire

                var selectedName = $('#nomcomplet').val();
                let nameParts = selectedName.split(" ");
                let prenom = nameParts[0];
                let nom = nameParts[1];

                // Appel à l'API pour vérifier la disponibilité de l'apprenant
                $.ajax({
                    url: 'https://app.quizzspot.fr/is_participant_available',
                    method: 'GET',
                    data: {
                        nom: encodeURIComponent(nom),
                        prenom: encodeURIComponent(prenom)
                    },
                    success: function (response) {
                        console.log(response.Nom_dispo);
                        if (response.Nom_dispo === 'true') {
                            // L'apprenant est disponible, (ré)activer le bouton
                            $('button[type="submit"]').prop('disabled', false);

                            // Envoyer une autre requête pour marquer l'apprenant comme indisponible
                            $.ajax({
                                url: 'https://app.quizzspot.fr/mark_connected',
                                method: 'GET',
                                data: {
                                    nom: encodeURIComponent(nom),
                                    prenom: encodeURIComponent(prenom)
                                },
                                success: function (response) {
                                    // Ajouter les paramètres nom et prenom à l'URL de redirection
                                    var form = $('#loginForm');
                                    form.attr('action', 'attente.php?nom=' + encodeURIComponent(nom) + '&prenom=' + encodeURIComponent(prenom));
                                    form.submit();
                                },
                                error: function (xhr, status, error) {
                                    console.error('Erreur lors de la mise à jour de la disponibilité de l\'apprenant : ' + error);
                                }
                            });
                        } else {
                            // L'apprenant n'est pas disponible
                            alert('Cet apprenant est déjà connecté.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Erreur lors de la vérification de la disponibilité de l\'apprenant : ' + error);
                    }
                });
            });
        });
    </script>
</body>

</html>
