<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Paramètres Session</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script>
			function retour(){
				window.location.href = ".";
			}
		</script>
    </head>

    <body>
        <div class="row m-4">
			<img class="col-auto" src="../retour.png" alt="Retour" style="width:5%;" onclick="retour()">
			<div class="col row justify-content-center">
                <?php
                echo '<h3 class="col-auto align-self-center" style="margin-right:7%;">Page';
                // Vérifie dans l'url si il y a le paramètre id_session, s'il y est c'est une modification sinon une création
                if (isset($_GET['id_session'])) {
                    echo ' Modification ';
                } else {
                    echo ' Création ';
                }
                echo 'Session</h3>';
				?>
			</div>
		</div>
		<div class="mt-5 container">
            <div class="row justify-content-center">
                <form action="" method="post" class="col-auto">
                    <?php
                    // Appelle l'api PHP
                    require_once "../query.php";
                    // Si c'est une modification, récupère le nom, l'id du groupe et du quizz dont fait parti la session et son activation
                    if (isset($_GET['id_session'])) {
                        $infos=request("SELECT nom_session, id_groupe, id_quizz, bool_session FROM sessions WHERE id_session=".$_GET['id_session'].";")[1];
                    }
                    echo '<table class="border border-black border-2 table table-striped fs-4">';
                        echo '<tr>';
                            echo '<td class="border-end border-black">Nom de session</td>';
                            echo '<td>';
                                echo '<input class="form-control" type="text" id="nom_session" name="nom_session" required max="100"';
                                // Si c'est une modification, affiche le nom de la session
                                if (isset($_GET['id_session'])) {
                                    echo ' value="'.$infos[1].'"';
                                }
                                echo '/>';
                            echo '</td>';
                        echo '</tr>';
                        echo '<tr>';
                            echo '<td class="border-end border-black">Groupe</td>';
                            echo '<td>';
                                echo '<select class="form-select" id="id_groupe" name="id_groupe" required>';
                                    echo '<option value="">Sélectionner un Groupe</option>';
                                    // Récupère l'id et le nom des groupes activés
                                    $json_groupes=request("SELECT id_groupe, nom_groupe FROM groupes where bool_groupe=1;");
                                    // Parcours les groupes
                                    foreach ($json_groupes as $groupe){
                                        echo '<option value="'.$groupe[1].'"';
                                        // Si c'est une modification, et que l'id du groupe est égal à celui dont fait parti la session, sélection du groupe
                                        if (isset($_GET['id_session']) && $groupe[1]==$infos[2]) {
                                            echo ' selected';
                                        }
                                        echo '>'.$groupe[2].'</option>';
                                    }
                                echo '</select>';
                            echo '</td>';
                        echo'</tr>';
                        echo '<tr>';
                            echo '<td class="border-end border-black">Quizz</td>';
                            echo '<td>';
                                echo '<select class="form-select" id="id_quizz" name="id_quizz" required>';
                                    echo '<option value="">Sélectionner un Quizz</option>';
                                    // Récupère l'id et le nom des quizz activés
                                    $json_quizz=request("SELECT id_quizz, nom_quizz FROM quizzs where bool_quizz=1;");
                                    // Parcours les quizz
                                    foreach ($json_quizz as $quizz){
                                        echo '<option value="'.$quizz[1].'"';
                                        // Si c'est une modification, et que l'id du quizz est égal à celui dont fait parti la session, sélection du quizz
                                        if (isset($_GET['id_session']) && $quizz[1]==$infos[3]) {
                                            echo ' selected';
                                        }
                                        echo '>'.$quizz[2].'</option>';
                                    }
                                echo '</select>';
                            echo '</td>';
                        echo '</tr>';
                        // Si c'est une modification, affiche son activation
                        if (isset($_GET['id_session'])) {
                            echo '<tr>';
                                echo '<td class="border-end border-black">Activé</td>';
                                echo '<td>';
                                    echo '<input class="form-check-input" type="checkbox" id="bool_session" name="bool_session"';
                                        // S'il est activé, coche la checkbox
                                        if ($infos[4]=="1") {
                                            echo ' checked';
                                        }
                                    echo '/>';
                                echo '</td>';
                            echo '</tr>';
                        }  
                    echo '</table>';
                    echo '<div class="row justify-content-center">';
                        echo '<button type="submit" class="col-auto btn btn-lg btn-success">';
                        // Modifie le nom du bouton selon le mode
                        if (isset($_GET['id_session'])) {
                            echo 'Modifier';
                        } else {
                            echo 'Créer';
                        }  
                        echo '</button>';
                    echo '</div>';
                echo '</form>';

                // Si la page a été soumise (le bouton avec le type "submit" a été clické)
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (isset($_GET['id_session'])) {
                        // Si c'est une modification, récupère son activation et la convertit pour la requête SQL
                        if ($_POST['bool_session']=="on") {
                            $boolean="1";
                        } else {
                            $boolean="0";
                        }
                        // Prépare la requête pour modifier le nom et l'activation de la session
                        $requete_sql='update sessions set nom_session="'.$_POST['nom_session'].'", id_groupe='.$_POST['id_groupe'].', id_quizz='.$_POST['id_quizz'].', bool_session='.$boolean.' where id_session='.$_GET['id_session'].';';
                    } else {
                        // Prépare la requête pour créer la session avec le nom et l'activation à oui par défault
                        $requete_sql='insert into sessions(nom_session, id_groupe, id_quizz, bool_session) values ("'.$_POST['nom_session'].'",'.$_POST['id_groupe'].','.$_POST['id_quizz'].',1)';
                    }
                    // Execute la requête
                    $requete=request($requete_sql);
                    // Retourne au menu des sessions
                    header("Location: .");
                }
                ?>

            </div>
		</div>
    </body>
</html>