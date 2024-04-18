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
                            echo '<select name="nomcomplet" id="nomcomplet" required>';
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
                            echo "<p>Tous les participants inscrits sont déjà connectés</p>";
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
    //Vérifier si l'apprenant est disponible, et le marquer indisponible après sélection
    $(document).ready(function() {
        // Lorsqu'un nom est sélectionné dans la liste
        $('#nomcomplet').on('change', function() {
            var selectedName = $(this).val();
            if (selectedName !== '') {
                // Appel à l'API pour vérifier la disponibilité de l'apprenant
                $.ajax({
                    url: 'http://localhost/SiteQuizz/verif_apprenant.php?nomcomplet=' + encodeURIComponent(selectedName),
                    method: 'GET',
                    success: function(response) {
                        if (response.result === 'true') {
                            // L'apprenant est disponible, (ré)activer le bouton
                            $('button[type="submit"]').prop('disabled', false);
                            // Envoyer une autre requête pour marquer l'apprenant comme indisponible
                            $.ajax({
                                url: 'http://localhost/SiteQuizz/indispo_apprenant.php?nomcomplet=' + encodeURIComponent(selectedName),
                                method: 'GET',
                                success: function(response) {
                                    // Rediriger vers la page d'attente
                                    $('#loginForm').submit();
                                },
                                error: function(xhr, status, error) {
                                    console.error('Erreur lors de la mise à jour de la disponibilité de l\'apprenant : ' + error);
                                }
                            });
                        } else {
                            // L'apprenant n'est pas disponible
                            alert('Cet apprenant est déjà connecté.');
                            // Désactiver le bouton de soumission du formulaire
                            $('button[type="submit"]').prop('disabled', true);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur lors de la vérification de la disponibilité de l\'apprenant : ' + error);
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
