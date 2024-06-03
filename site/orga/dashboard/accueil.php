<?php
session_start();
include 'query.php'; // Inclure le fichier contenant la fonction db_connect()

// Vérifie si l'utilisateur est connecté, sinon le redirige vers index.php
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION["user"];
$role = $user['role_user'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizz - Status</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        #admin, #apprenant, #formateur {
            cursor: pointer;
        }
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
                <h1 class="col-auto align-self-center" style="margin-right:7%;">Site organisation Quiz</h1>
            </div>
        </div>
        <div class="row justify-content-center">
            <h2 class="text-center">Sélectionner votre statut</h2>
        </div>
    </div>
           
    <div class="row justify-content-around mt-5">
        <?php if ($role === 'administrateur') { ?>
            <div id="admin" class="col-md-3 text-center p-5 border border-dark border-3 bg-success text-white rounded-5">
                <h4>Administrateur</h4>
            </div>
        <?php } ?>
        <div id="apprenant" class="col-md-3 text-center p-5 border border-dark border-3 bg-success text-white rounded-5">
            <h4>Résultats des Apprenants</h4>
        </div>
        <?php if ($role === 'administrateur' || $role === 'formateur') { ?>
            <div id="formateur" class="col-md-3 text-center p-5 border border-dark border-3 bg-success text-white rounded-5">
                <h4>Formateur</h4>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    <?php if ($role === 'administrateur') { ?>
        var adminDiv = document.getElementById('admin');
        adminDiv.addEventListener('click', function() {
            window.location.href = "administrateur/index.php";
        });
    <?php } ?>
    
    var apprenantDiv = document.getElementById('apprenant');
    apprenantDiv.addEventListener('click', function() {
        window.location.href = "apprenant/index.php";
    });

    <?php if ($role === 'administrateur' || $role === 'formateur') { ?>
        var formateurDiv = document.getElementById('formateur');
        formateurDiv.addEventListener('click', function() {
            window.location.href = "formateur/index.php";
        });
    <?php } ?>
	
		function retour() {
			window.location.href="index.php";
		}
</script>
</body>
</html>
