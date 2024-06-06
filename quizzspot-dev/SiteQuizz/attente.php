<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'attente</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <script>
        $(document).ready(function () {
            // Récupérer les paramètres de l'URL
            const urlParams = new URLSearchParams(window.location.search);
            const nomcomplet = urlParams.get('nomcomplet');

            if (!nomcomplet) {
                console.error('Nom complet manquant.');
                return;
            }

            // Séparer la chaîne en utilisant l'espace comme délimiteur
            let nameParts = nomcomplet.split(" ");
            let prenom = nameParts[0];
            let nom = nameParts[1];

            if (!prenom || !nom) {
                console.error('Prénom ou nom manquant.');
                return;
            }

            // Fonction pour vérifier le statut du quizz
            function checkQuizzStatus() {
                $.ajax({
                    url: 'https://app.quizzspot.fr/is_quizz_started',
                    method: 'GET',
                    success: function (response) {
                        console.log(response.msg);
                        if (response.msg === true) {
                            $('#status').text('Le quizz a commencé !');
                            // Rediriger l'utilisateur vers la page du quizz
                            window.location.href = 'question.php';
                        } else {
                            $('#status').text('En attente du début du quizz...');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Erreur lors de la vérification du statut du quizz : ' + error);
                    }
                });
            }

            setInterval(checkQuizzStatus, 2000); // Vérifier toutes les 2 secondes

            // Fonction pour remettre l'apprenant en disponible via l'API
            function setAvailable(nom, prenom) {
                $.ajax({
                    url: 'https://app.quizzspot.fr/unmark_connected',
                    method: 'GET',
                    data: {
                        nom: encodeURIComponent(nom),
                        prenom: encodeURIComponent(prenom)
                    },
                    success: function (response) {
                        console.log("OSKOUR")
                        // window.location.href = 'connexion.html';
                    },
                    error: function (xhr, status, error) {
                        console.error('Erreur lors de la mise à jour de la disponibilité de l\'apprenant : ' + error);
                    }
                });
            }

            // Ajouter l'événement pour remettre l'apprenant en disponible
            $('#setAvailableLink').on('click', function () {
                setAvailable(nom, prenom);
            });

        });
    </script>
    <div class="container">
        <h1>Bienvenue !</h1>
        <?php
        // Vérifier si un nom complet a été sélectionné
        if (isset($_GET['nomcomplet'])) {
            // Récupérer le nom complet sélectionné
            $nomcomplet = $_GET['nomcomplet'];
            echo "<p>Connecté en tant que : <b>$nomcomplet</b></p>";
            $token = uniqid();
            setcookie("token", $token, time() + (7200), "/"); // Token dans cookie valide pendant 2h
            echo "<p>Le Quizz commencera sous peu.</p>";
            echo "<p>DEBUG : VOTRE TOKEN EST $token</p>";
            echo "<p>Vous n'êtes pas $nomcomplet ? <a href='connexion.html' id='setAvailableLink'>Retour</a></p>";
        } else {
            echo "<p>Vous n'êtes pas connecté. <a href='connexion.html'>Sélectionnez votre nom</a></p>";
        }
        ?>
        <p id="status">En attente du début du quizz...</p>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const nomcomplet = urlParams.get('nomcomplet');
        const token = <?php echo json_encode($token); ?>;
        console.log(token);
        console.log(nomcomplet);
        $.ajax({
                    url: 'https://app.quizzspot.fr/insert_token', // URL de l'API pour l'insertion du token
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ token: token, nom_complet: nomcomplet }),
                    success: function(response) {
                        console.log('Token inséré avec succès dans la base de données.');
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur lors de l\'insertion du token dans la base de données : ' + error);
                    }
                });
    </script>

</body>

</html>
