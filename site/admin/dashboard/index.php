<?php
session_start();

include '../query.php';
$conn = db_connect();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["username"])) {
    header("Location: ../index.php"); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

$query = "SELECT id_user, prenom_user, nom_user, email_user, bool_user FROM utilisateurs WHERE role_user = 'formateur'";
$result = $conn->query($query);

?>
<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page administrateur paramètres Formateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        #retour {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row m-4">
            <img id="retour" class="col-auto" src="../img/retour.png" alt="Retour" style="width:5%;" onclick="retour()">
            <div class="col row justify-content-center">
                <h1 class="col-auto align-self-center" style="margin-right:7%;">Page administrateur paramètres Formateurs</h1>
            </div>
        </div>
        <div class="row justify-content-center mt-5">
            <button id="create" type="button" class="col-auto btn btn-success p-1 border border-black border-2">Créer Formateur</button>
        </div>
        <div class="row row-cols-auto justify-content-center">
        <?php
        // Vérifier s'il y a des formateurs
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id_utilisateur = $row["id_user"];
                $prenom = $row["prenom_user"];
                $nom = $row["nom_user"];
                $email = $row["email_user"];
                $statut = $row["bool_user"];
        ?>
                <div class="col">
                    <div class="row mt-5">
                        <div class="col-auto m-4 bg-secondary-subtle rounded-5 border border-black border-2">
                            <div class="row">
								 <div class="col">
                                    <!-- Bouton Modifier -->
                                    <button type="button" class="col-auto btn btn-info p-1 border border-black border-2" onclick="modifier(<?php echo $id_utilisateur; ?>)">Modifier</button>
                                </div>
								 <div class="col align-self-center">
                                    <h5 class="text-primary fs-6">Formateur</h5>
                                </div>
								<div class="col">
                                    <?php if ($statut == 1): ?>
                                        <button type="button" class="col-auto btn btn-danger p-1 border border-black border-2" onclick="toggleUserStatus(<?php echo $id_utilisateur; ?>, 0)">Désactiver</button>
                                    <?php else: ?>
                                        <button type="button" class="col-auto btn btn-success p-1 border border-black border-2" onclick="toggleUserStatus(<?php echo $id_utilisateur; ?>, 1)">Activer</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <p class="mt-2 text-center"><?php echo $prenom . " " . $nom; ?></p>
                            <div class="mt-0 row text-center">
                                <h5 class="text-primary fs-6">Adresse mail :</h5>
                                <p><?php echo $email; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<p class='text-center mt-5'>Aucun formateur trouvé.</p>";
        }
        $conn->close();
        ?>
        </div>
    </div>
    <script>
        var createBtn = document.getElementById('create');

        // Fonction pour rediriger vers la page de modification avec l'ID de l'utilisateur dans l'URL
        function modifier(id_utilisateur) {
            window.location.href = "modif.php?id=" + id_utilisateur;
        }

        createBtn.addEventListener('click', function() {
            window.location.href = "create.php";
        });

        function toggleUserStatus(userId, status) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_user_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Recharger la page pour mettre à jour la liste des utilisateurs après le changement de statut
                    location.reload();
                }
            };
            xhr.send("userId=" + userId + "&status=" + status);
        }

        function retour() {
            window.location.href = "../index.php";
        }
    </script>
</body>
</html>
