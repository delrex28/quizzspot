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

    <body>
        <div class="row m-4">
			<img class="col-auto" onclick="retour()" src="../retour.png" alt="Retour" style="width:5%;">
			<div class="col row justify-content-center">
				<h3 class="col-auto align-self-center">Page Formateur Paramètres Groupes</h3>
			</div>
			<button type="button" class="col-auto btn btn-lg btn-success" onclick="groupe(``)">Créer un groupe</button>
		</div>
		<div class="mt-5">
			<div class="container">
				<div class="row row-cols-lg-4 justify-content-center">
					<?php
                    // Appelle l'api PHP
					require_once "../query.php";
                    // Récupère l'id, le nom et le nombre d'apprenants dans les groupes
                    $json_groupes=request("SELECT groupes.id_groupe, groupes.nom_groupe, COUNT(rel_utilisateurs_groupes.id_user) FROM groupes LEFT JOIN rel_utilisateurs_groupes ON groupes.id_groupe = rel_utilisateurs_groupes.id_groupe GROUP BY groupes.id_groupe, groupes.nom_groupe;");
					// Parcours les apprenants
                    foreach ($json_groupes as $groupe){
                        // Créé la bulle du groupe
						echo '<div class="col-auto m-2 bg-secondary-subtle rounded-4 border border-black p-3">';
							echo '<div class="row">';
                                // Créé le bouton modifier avec l'id du groupe
								echo '<button type="button" class="col-auto btn btn-info" onclick="groupe(`?id_groupe='.$groupe[1].'`)">Modifier</button>';
								echo '<div class="col align-self-center">';
									echo '<h5 class="text-info mx-4" style="text-decoration: underline;">Groupe</h5>';
								echo '</div>';
							echo '</div>';
                            // Affiche le nom du groupe
							echo '<p class="mt-2 text-center" style="font-weight: bold;">'.$groupe[2].'</p>';
							echo '<div class="mt-1 row text-center">';
								echo '<h5 class="text-primary" style="text-decoration: underline;">Nombre apprenants​</h5>';
								// Affiche le nombre d'apprenants dans le groupe
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