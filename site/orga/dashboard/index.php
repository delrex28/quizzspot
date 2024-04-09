<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Selection Status</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<script>
			function retour() {
				window.location.href="../";
			}
		</script>
    </head>

    <body>
		<div class="row m-4">
			<img class="col-auto" src="./retour.png" alt="Retour" onclick="retour()" style="width:5%;">
			<div class="col row justify-content-center"><h3 class="col-auto align-self-center" style="margin-right:7%;">Page Sélection Formateur</h3></div>
		</div>
		<div class="mt-5 container">
		<?php
            // Scan tout ce que se trouve dans le même dossier
			$dirs = scandir(".");
            // Parcours toutes les recherches
			foreach ($dirs as $dir) {
                // Si c'est un dossier et qu'il ne se nomme pas . et ..
				if (is_dir($dir) && $dir!="." && $dir!="..") {
                    // Affiche le dossier
					echo '<div class="row justify-content-center mt-3"><a class="col-auto p-3 fs-3 text-center border border-black rounded-4 bg-success" style="text-decoration:none; color:white; width:50%;" href="./'.$dir.'">'.ucfirst($dir).'</a></div>';
				}	
			}
		?>
    </body>
</html>