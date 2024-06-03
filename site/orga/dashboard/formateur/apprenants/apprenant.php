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
        <title>Paramètres Apprenant</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script>
			function retour(){
				window.location.href = ".";
			}
		</script>
    </head>

    <body>
 		<div class="row m-4">
			<img class="col-auto" src="../../img/retour.png" alt="Retour" style="width:5%;" onclick="retour()">
			<div class="col row justify-content-center">
                <?php
                echo '<h3 class="col-auto align-self-center" style="margin-right:7%;">Page';
                if (isset($_GET['id_apprenant'])) {
                    echo ' Modification ';
                } else {
                    echo ' Création ';
                }
                echo 'Apprenant</h3>';
				?>
			</div>
		</div>
		<div class="mt-5 container">
            <div class="row justify-content-center">
                <form action="" method="post" class="col-auto">
                    <table class="border border-black border-2 table table-striped fs-4">
                        <?php
                            require_once "../query.php";
                            if (isset($_GET['id_apprenant'])) {
                                $infos_user=request("SELECT nom_user, prenom_user, bool_user FROM utilisateurs where id_user=".$_GET['id_apprenant'].";")[1];
                            }
                        echo '<tr>';
                            echo '<td class="border-end border-black">Prénom</td>';
                            echo '<td><input class="form-control" type="text" id="prenom_user" name="prenom_user" required';
                            if (isset($_GET['id_apprenant'])) {
                                echo ' value="'.$infos_user[2].'"';
                            }
                            echo '/></td>';
                        echo '</tr>';
                        echo '<tr>';
                            echo '<td class="border-end border-black">Nom</td>';
                            echo '<td><input class="form-control" type="text" id="nom_user" name="nom_user" required';
                            if (isset($_GET['id_apprenant'])) {
                                echo ' value="'.$infos_user[1].'"';
                            }
                            echo '/></td>';
                        echo '</tr>';
                        if (isset($_GET['id_apprenant'])) {
                            echo '<tr>';
                                echo '<td class="border-end border-black">Activé</td>';
                                echo '<td>';
                                    echo '<input class="form-check-input" type="checkbox" id="bool_user" name="bool_user"';
                                        if ($infos_user[3]==1) {
                                            echo ' checked';
                                        }
                                    echo '/>';
                                echo '</td>';
                            echo '</tr>';
                        }
                    echo '</table>';
                    echo '<div class="row justify-content-center">';
                        echo '<button type="submit" class="col-auto btn btn-lg btn-success">';
                        if (isset($_GET['id_apprenant'])) {
                            echo 'Modifier';
                        } else {
                            echo 'Créer';
                        }  
                        echo '</button>';
                    echo '</div>';
                echo '</form>';
                
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (isset($_GET['id_apprenant'])) {
                        if ($_POST['bool_user']=="on") {
                            $boolean="1";
                        } else {
                            $boolean="0";
                        }
                        $requete_sql='update utilisateurs set nom_user="'.$_POST['nom_user'].'", prenom_user="'.$_POST['prenom_user'].'", bool_user='.$boolean.' where id_user='.$_GET['id_apprenant'].';';
                    } else {
                        $requete_sql='insert into utilisateurs (nom_user, prenom_user, bool_user, role_user) values("'.$_POST['nom_user'].'", "'.$_POST['prenom_user'].'", 1, "apprenant");';
                    }
                    $requete=request($requete_sql);
                    header("Location: .");
                }
                ?>
            </div>
		</div>
    </body>
</html>