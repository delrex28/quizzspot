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
        <title>Paramètres Groupes</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<script>
			function question(mode){
				window.location.href = "./question.php"+mode;
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
				<h3 class="col-auto align-self-center">Page Formateur Paramètres Question</h3>
			</div>
			<button type="button" class="col-auto btn btn-lg btn-success" onclick="question(``)">Créer une question</button>
		</div>
		<div class="mt-5">
			<div class="container">
				<div class="row row-cols-lg-4 justify-content-center">
					<?php
					require_once "../query.php";
                    $json_questions=request("SELECT questions.id_question, questions.intitule_question, quizzs.nom_quizz, categories.nom_categorie FROM questions left join categories on questions.id_categorie=categories.id_categorie left join quizzs on questions.id_quizz=quizzs.id_quizz group by questions.id_question, questions.intitule_question;");
					foreach ($json_questions as $question){
						echo '<div class="col-auto m-2 bg-secondary-subtle rounded-4 border border-black p-3">';
							echo '<div class="row">';
								echo '<button type="button" class="col-auto btn btn-info" onclick="question(`?id_question='.$question[1].'`)">Modifier</button>';
								echo '<div class="col align-self-center">';
									echo '<h5 class="text-info mx-4" style="text-decoration: underline;">Question</h5>';
								echo '</div>';
							echo '</div>';
							echo '<p class="mt-2 text-center" style="font-weight: bold;">'.$question[2].'</p>';
                            echo '<div class="mt-1 row text-center">';
								echo '<h5 class="text-primary" style="text-decoration: underline;">Numéro de la Question</h5>';
								echo '<p>Question N°'.$question[1].'</p>';
							echo '</div>';
                            echo '<div class="mt-1 row text-center">';
								echo '<h5 class="text-primary" style="text-decoration: underline;">Quizz de la Question</h5>';
								echo '<p>'.$question[3].'</p>';
							echo '</div>';
                            echo '<div class="mt-1 row text-center">';
								echo '<h5 class="text-primary" style="text-decoration: underline;">Groupe de la Question</h5>';
								echo '<p>'.$question[4].'</p>';
							echo '</div>';
						echo '</div>';
					}
					?>
				</div>
			</div>
		</div>
    </body>
</html>