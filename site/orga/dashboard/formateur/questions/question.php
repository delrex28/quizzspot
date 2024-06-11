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
        <title>Paramètres Question</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script>
			function retour(){
				window.location.href = ".";
			}

            function verif() {
                button_remove = document.querySelector("#button_remove");
                button_remove.style.display="block";

                elementsChoix = document.querySelectorAll(".choix");
                // Sélectionner tous les éléments avec la classe "choix"
                if (elementsChoix.length == 2) {
                    button_remove.style.display="none";
                }
            }

            function add(){
                // Créer un élément de ligne de tableau
                nouvelleLigne = document.createElement("tr");
                nouvelleLigne.classList.add("choix", "border", "border-black", "border-bottom", "bg-secondary-subtle");

                // Sélectionner tous les éléments avec la classe "choix"
                elementsChoix = document.querySelectorAll(".choix");

                // Obtenir le nombre d'éléments avec la classe "choix"
                number=elementsChoix.length+1;
                // Créer le contenu de la ligne
                contenuLigne = `
                    <td class="px-2">Choix N°`+number+`</td>
                    <td class="border-start border-end border-black"><input class="form-control" type="text" id="contenu_reponse_`+number+`" name="contenu_reponse_`+number+`" required></td>
                    <td class="text-center"><input class="form-check-input bg-secondary" type="checkbox" id="bonne_reponse_`+number+`" name="bonne_reponse_`+number+`"></td>
                `;

                // Ajouter le contenu à la ligne
                nouvelleLigne.innerHTML = contenuLigne;

                // Sélectionner le tableau et ajouter la nouvelle ligne
                tableau = document.querySelector("#table_questions");
                tableau.appendChild(nouvelleLigne);
                verif()
            }

            function remove(){
                // Sélectionner tous les éléments avec la classe "choix"
                elementsChoix = document.querySelectorAll(".choix");

                // Vérifier si le nombre d'éléments est supérieur à 2
                if (elementsChoix.length > 2) {
                    // Sélectionner le dernier élément avec la classe "choix"
                    dernierElement = elementsChoix[elementsChoix.length - 1];
                    
                    // Supprimer le dernier élément du tableau
                    dernierElement.parentNode.removeChild(dernierElement);
                }
                verif()
            }
		</script>
    </head>

    <body onload="verif()">
        <div class="row m-4">
			<img class="col-auto" src="../../img/retour.png" alt="Retour" style="width:5%;" onclick="retour()">
			<div class="col row justify-content-center">
                <?php
                echo '<h3 class="col-auto align-self-center" style="margin-right:7%;">Page';
                if (isset($_GET['id_question'])) {
                    echo ' Modification ';
                } else {
                    echo ' Création ';
                }
                echo 'Question</h3>';
				?>
			</div>
		</div>
		<div class="mt-5 container">
            <div class="row justify-content-center">
                <form action="" method="post" class="col-auto">
                    <?php
                    require_once "../query.php";

                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $requetes_sql=[];
                        if (isset($_GET['id_question'])) {
                            $id_question=$_GET['id_question'];

                            if ($_POST['bool_question']=="on") {
                                $boolean="1";
                            } else {
                                $boolean="0";
                            }
                            $query='update questions set intitule_question="'.$_POST['intitule_question'].'", bool_question='.$boolean.', id_niveau='.$_POST['id_niveau'].', id_categorie='.$_POST['id_categorie'].' where id_question='.$_GET['id_question'].';';
                            array_push($requetes_sql, $query);
                            $query='delete from reponses where id_question='.$_GET['id_question'].';';
                            array_push($requetes_sql, $query);
                        } else {
                            $query='INSERT INTO questions (intitule_question, bool_question, id_niveau, id_categorie) VALUES ("'.$_POST['intitule_question'].'", 1, '.$_POST['id_niveau'].', '.$_POST['id_categorie'].');';
                            $id_question=request($query);
                        }

                        $number=1;
                        $fin=FALSE;
                        while ($fin!=TRUE):
                            if(!isset($_POST['contenu_reponse_'.$number])) {
                                $fin=TRUE;
                            } else {
                                if ($_POST['bonne_reponse_'.$number]=="on") {
                                    $boolean="1";
                                } else {
                                    $boolean="0";
                                }
                                
                                $query='INSERT INTO reponses (contenu_reponse, bonne_reponse, id_question) VALUES ("'.$_POST['contenu_reponse_'.$number].'", "'.$boolean.'", "'.$id_question.'");';
                                array_push($requetes_sql, $query);
                                $number++;
                            }
                        endwhile;

                        foreach ($requetes_sql as $query) {
                            $requests=request($query);
                        }
                        
                        header("Location: .");
                    }
                    
                    if (isset($_GET['id_question'])) {
                        $infos=request('select intitule_question, bool_question, id_niveau, id_categorie from questions where id_question='.$_GET['id_question'].';')[1];
                    }
                    echo '<table id="table_questions" class="border border-black border-2 table table-striped fs-4">';
                        echo '<tr>';
                            echo '<td>Nom Question</td>';
                            echo '<td class="border-start border-end border-black"><input class="form-control" type="text" id="intitule_question" name="intitule_question" required';
                            if (isset($_GET['id_question'])) {
                                echo ' value="'.$infos[1].'"';
                            }
                            echo '></td>';
                            echo '<td>Réponses</td>';
                        echo '</tr>';

                        echo '<tr>';
                            echo '<td class="border-end border-black">Niveau</td>';
                            echo '<td>';
                                $niveaux=request('select id_niveau, nom_niveau from niveau;');
                                echo '<select class="form-select" name="id_niveau" id="id_niveau-select" required>';
                                foreach ($niveaux as $niveau) {
                                    echo '<option value="'.$niveau[1].'"';
                                    if (isset($_GET['id_question'])) {
                                        if ($infos[3]==$niveau[1]) {
                                            echo ' selected';
                                        }
                                    }
                                    echo '>'.$niveau[2].'</option>';
                                }
                                echo '</select>';
                            echo '</td>';
                            echo '<td></td>';
                        echo '</tr>';
					
						echo '<tr>';
                            echo '<td class="border-end border-black">Catégorie</td>';
                            echo '<td>';
                                $niveaux=request('select id_categorie, nom_categorie from categories where bool_categorie=1;');
                                echo '<select class="form-select" name="id_categorie" id="id_categorie" required>';
                                foreach ($niveaux as $niveau) {
                                    echo '<option value="'.$niveau[1].'"';
                                    if (isset($_GET['id_question'])) {
                                        if ($infos[3]==$niveau[1]) {
                                            echo ' selected';
                                        }
                                    }
                                    echo '>'.$niveau[2].'</option>';
                                }
                                echo '</select>';
                            echo '</td>';
                            echo '<td></td>';
                        echo '</tr>';

                        if (isset($_GET['id_question'])) {
                            echo '<tr>';
                                echo '<td class="border-end border-black">Activé</td>';
                                echo '<td>';
                                    echo '<input class="form-check-input" type="checkbox" id="bool_question" name="bool_question"';
                                        if ($infos[2]==1) {
                                            echo ' checked';
                                        }
                                    echo '/>';
                                echo '</td>';
                                echo '<td></td>';
                            echo '</tr>';
                        }

                if (!isset($_GET['id_question'])) {
                    for ($i=1; $i<=2; $i++) {
                        echo '<tr class="choix">';
                            echo '<td>Choix N°'.$i.'</td>';
                            echo '<td class="border-start border-end border-black"><input class="form-control" type="text" id="contenu_reponse_'.$i.'" name="contenu_reponse_'.$i.'" required></td>';
                            echo '<td class="text-center"><input class="form-check-input bg-secondary" type="checkbox" id="bonne_reponse_'.$i.'" name="bonne_reponse_'.$i.'"></td>';
                        echo '</tr>';
                    }
                } else {
                    $reponses=request('select id_reponse, contenu_reponse, bonne_reponse from reponses where id_question='.$_GET['id_question'].';');
                    for ($i=1; $i<=count($reponses); $i++){
                        echo '<tr class="choix">';
                            echo '<input type="hidden" id="id_reponse_'.$i.'" name="id_reponse_'.$i.'" value="'.$reponses[$i][1].'">';
                            echo '<td>Choix N°'.$i.'</td>';
                            echo '<td class="border-start border-end border-black"><input class="form-control" type="text" id="contenu_reponse_'.$i.'" name="contenu_reponse_'.$i.'" value="'.$reponses[$i][2].'" required></td>';
                            echo '<td class="text-center"><input class="form-check-input';
                            if ($reponses[$i][3]==1) {
                                echo '" checked';
                            } else {
                                echo ' bg-secondary"';
                            }
                            echo ' type="checkbox" id="bonne_reponse_'.$i.'" name="bonne_reponse_'.$i.'"';
                            
                            echo '></td>';
                        echo '</tr>';
                    }
                }
                    echo '</table>';
                    echo '<div class="row justify-content-evenly">';
                        echo '<button type="button" class="col-auto btn btn-lg btn-primary" onclick="add()">Ajouter</button>';
                        echo '<button type="button" class="col-auto btn btn-lg btn-danger"onclick="remove()" id="button_remove">Supprimer le dernier</button>';
                        echo '<button type="submit" class="col-auto btn btn-lg btn-success">';
                        if (isset($_GET['id_question'])) {
                            echo 'Modifier';
                        } else {
                            echo 'Créer';
                        }  
                        echo '</button>';
                    echo '</div>';
                ?>
                </form>
            </div>
		</div>
    </body>
</html>