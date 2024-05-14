<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Paramètres Apprenants</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<script>
			function apprenant(mode){
				window.location.href = "./apprenant.php"+mode;
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
				<h3 class="col-auto align-self-center">Page Formateur Paramètres Apprenants</h3>
			</div>
			<button type="button" class="col-auto btn btn-lg btn-success" onclick="apprenant(``)">Créer un apprenant</button>
		</div>
		<div class="mt-5">
			<div class="container">
				<div class="row row-cols-lg-4 justify-content-center">
					<?php
                    // Appelle l'api PHP
					require_once "../query.php";
                    // Récupère l'id et le nom prénom sous forme "nom prénom" des utilisateurs avec le rôle "apprenants"
                    $json_apprenants=request('SELECT id_user, CONCAT(nom_user," ",prenom_user) FROM utilisateurs where role_user="apprenants";');
					// Parcours les apprenants
                    foreach ($json_apprenants as $apprenant){
                        // Créé la bulle de l'apprenant
						echo '<div class="col-auto m-2 bg-secondary-subtle rounded-4 border border-black p-3">';
							echo '<div class="row">';
                                // Créé le bouton modifier avec l'id de l'apprenant
								echo '<button type="button" class="col-auto btn btn-info" onclick="apprenant(`?id_apprenant='.$apprenant[1].'`)">Modifier</button>';
								echo '<div class="col align-self-center">';
									echo '<h5 class="text-info mx-4" style="text-decoration: underline;">Apprenant</h5>';
								echo '</div>';
							echo '</div>';
                            // Affiche le "nom prénom" de l'apprenant
							echo '<p class="mt-2 text-center" style="font-weight: bold;">'.$apprenant[2].'</p>';
							echo '<div class="mt-1 row text-center">';
								echo '<h5 class="text-primary" style="text-decoration: underline;">Appartient aux groupes</h5>';
                                // Récupère les groupes dont fait parti l'apprenant
                                $json_groupes=request('SELECT groupes.nom_groupe FROM groupes inner join rel_utilisateurs_groupes on rel_utilisateurs_groupes.id_groupe=groupes.id_groupe where rel_utilisateurs_groupes.id_user='.$apprenant[1].';');
                                echo '<ul style="list-style: none;">';
                                // Créé une liste et ajoute les groupes à celle-ci
                                foreach ($json_groupes as $groupe) {
                                    echo '<li>'.$groupe[1].'</li>';
                                }
                                echo '</ul>';
							echo '</div>';
						echo '</div>';
					}
					?>
				</div>
			</div>
		</div>
    </body>
</html>