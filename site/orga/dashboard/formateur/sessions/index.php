<?php
session_start();
include '../query.php'; // Inclure le fichier contenant la fonction db_connect()

// Vérifie si l'utilisateur est connecté, sinon le redirige vers index.php
if (!isset($_SESSION["user"])) {
    header("Location: ../index.php");
    exit();
}

$user = $_SESSION["user"];
$role = $user['role_user'];
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Paramètres Sessions</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<script>
			function session(mode){
				window.location.href = "./session.php"+mode;
			}
			function retour(){
				window.location.href = "..";
			}
		</script>
	</head>

    <body class="" style="">
 		<div class="row m-4">
			<img class="col-auto" onclick="retour()" src="../../img/retour.png" alt="Retour" style="width:5%;">
			<div class="col row justify-content-center">
				<h3 class="col-auto align-self-center">Page Formateur Paramètres Sessions</h3>
			</div>
			<button type="button" class="col-auto btn btn-lg btn-success" onclick="session(``)">Créer une session</button>
		</div>
		<div class="mt-5">
			<div class="container">
				<div class="row row-cols-lg-4 justify-content-center">
					<?php
					require_once "../query.php";
                    $json_sessions=request("SELECT sessions.id_session, sessions.nom_session, groupes.nom_groupe, quizzs.nom_quizz FROM `sessions` INNER JOIN groupes ON groupes.id_groupe=sessions.id_groupe INNER JOIN quizzs ON quizzs.id_quizz=sessions.id_quizz;");
					foreach ($json_sessions as $session){
						echo '<div class="col-auto m-2 bg-secondary-subtle rounded-4 border border-black p-3">';
							echo '<div class="row">';
								echo '<button type="button" class="col-auto btn btn-info" onclick="session(`?id_session='.$session[1].'`)">Modifier</button>';
								echo '<div class="col align-self-center">';
									echo '<h5 class="text-info mx-4" style="text-decoration: underline;">Session</h5>';
								echo '</div>';
							echo '</div>';
							echo '<p class="mt-2 text-center" style="font-weight: bold;">'.$session[2].'</p>';
							echo '<div class="mt-1 row text-center">';
								echo '<h5 class="text-primary" style="text-decoration: underline;">Numéro Session</h5>';
								echo '<p>Session n°'.$session[1].'</p>';
							echo '</div>';
							echo '<div class="mt-1 row text-center">';
								echo '<h5 class="text-primary" style="text-decoration: underline;">Groupe Session</h5>';
								echo '<p>'.$session[3].'</p>';
							echo '</div>';
							echo '<div class="mt-1 row text-center">';
								echo '<h5 class="text-primary" style="text-decoration: underline;">Quizz Session</h5>';
								echo '<p>'.$session[4].'</p>';
							echo '</div>';
						echo '</div>';
					}
					?>
				</div>
			</div>
		</div>
    </body>
</html>