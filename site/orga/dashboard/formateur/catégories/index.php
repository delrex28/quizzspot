<?php
session_start();

include '../../query.php';
$conn = db_connect();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: ../index.php"); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

// Requête pour récupérer les catégories
$query = "SELECT id_categorie, nom_categorie, bool_categorie FROM categories";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Catégories</title>
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
            <img id="retour" class="col-auto" src="../../img/retour.png" alt="Retour" style="width:5%;" onclick="retour()">
            <div class="col row justify-content-center">
                <h1 class="col-auto align-self-center" style="margin-right:7%;">Page Catégories</h1>
            </div>
        </div>
        <div class="row justify-content-center mt-5">
            <button id="create" type="button" class="col-auto btn btn-success p-1 border border-black border-2">Créer Catégorie</button>
        </div>
        <div class="row row-cols-auto justify-content-center">
        <?php
        // Vérifier s'il y a des catégories
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id_categorie = $row["id_categorie"];
                $nom_categorie = $row["nom_categorie"];
                $statut = $row["bool_categorie"];
        ?>
                <div class="col">
                    <div class="row mt-5">
                        <div class="col-auto m-4 bg-secondary-subtle rounded-5 border border-black border-2">
                            <div class="row">
                                <div class="col">
                                    <!-- Bouton Modifier -->
                                    <button type="button" class="col-auto btn btn-info p-1 border border-black border-2" onclick="modifier(<?php echo $id_categorie; ?>)">Modifier</button>
                                </div>
                                <div class="col align-self-center">
                                    <h5 class="text-primary fs-6">Catégorie: <?php echo $nom_categorie; ?></h5>
                                </div>
                                <div class="col">
                                    <?php if ($statut == 1): ?>
                                        <button type="button" class="col-auto btn btn-danger p-1 border border-black border-2" onclick="toggleCategorie(<?php echo $id_categorie; ?>, 0)">Désactiver</button>
                                    <?php else: ?>
                                        <button type="button" class="col-auto btn btn-success p-1 border border-black border-2" onclick="toggleCategorie(<?php echo $id_categorie; ?>, 1)">Activer</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<p>Aucune catégorie trouvée.</p>";
        }
        ?>
        </div>
    </div>

    <script>
        function retour() {
            window.location.href = '../index.php';
        }

        function modifier(id) {
            window.location.href = 'modif_categorie.php?id=' + id;
        }

        function toggleCategorie(id_categorie, new_status) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "toggle_categorie.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send("id_categorie=" + id_categorie + "&new_status=" + new_status);
        }

        document.getElementById("create").onclick = function () {
            window.location.href = 'create.php';
        };
    </script>
</body>
</html>
