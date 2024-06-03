<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Paramètres Groupes</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<script>
			function groupe(mode){
				window.location.href = "./groupe.php"+mode;
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
				<h3 class="col-auto align-self-center">Page Formateur Paramètres Groupes</h3>
			</div>
			<button type="button" class="col-auto btn btn-lg btn-success" onclick="groupe(``)">Créer un groupe</button>
		</div>
		<div class="mt-5">
			<div class="container">
				<div class="row row-cols-lg-4 justify-content-center">
					<?php
					require_once "../query.php";
                    $json_groupes=request("SELECT groupes.id_groupe, groupes.nom_groupe, COUNT(rel_utilisateurs_groupes.id_user) FROM groupes LEFT JOIN rel_utilisateurs_groupes ON groupes.id_groupe = rel_utilisateurs_groupes.id_groupe GROUP BY groupes.id_groupe, groupes.nom_groupe;");
					foreach ($json_groupes as $groupe){
						echo '<div class="col-auto m-2 bg-secondary-subtle rounded-4 border border-black p-3">';
							echo '<div class="row">';
								echo '<button type="button" class="col-auto btn btn-info" onclick="groupe(`?id_groupe='.$groupe[1].'`)">Modifier</button>';
								echo '<div class="col align-self-center">';
									echo '<h5 class="text-info mx-4" style="text-decoration: underline;">Groupe</h5>';
								echo '</div>';
							echo '</div>';
							echo '<p class="mt-2 text-center" style="font-weight: bold;">'.$groupe[2].'</p>';
							echo '<div class="mt-1 row text-center">';
								echo '<h5 class="text-primary" style="text-decoration: underline;">Nombre apprenants​</h5>';
								echo '<p>'.$groupe[3].'</p>';
							echo '</div>';
						echo '</div>';
					}
					?>
				</div>
			</div>
		</div>
    </body>
</html>