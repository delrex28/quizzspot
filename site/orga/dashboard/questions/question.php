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
                // Récupère le bouton "supprimer la dernière ligne"
                button_remove = document.querySelector("#button_remove");
                // L'affiche par défault
                button_remove.style.display="block";
                // Récupère le nombre d'élélements avec la classe choix
                elementsChoix = document.querySelectorAll(".choix");
                // Si il n'y en a que 2, cache le bouton de suppresion
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
			<img class="col-auto" src="../retour.png" alt="Retour" style="width:5%;" onclick="retour()">
			<div class="col row justify-content-center">
                <?php
                echo '<h3 class="col-auto align-self-center" style="margin-right:7%;">Page';
                // Vérifie dans l'url si il y a le paramètre id_question, s'il y est c'est une modification sinon une création
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
                    // Appelle l'api PHP
                    require_once "../query.php";
                    // Si la page a été soumise (le bouton avec le type "submit" a été clické)
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $requetes_sql=[];
                        if (isset($_GET['id_question'])) {
                            // Si c'est une modification, récupère son activation et la convertit pour la requête SQL
                            $id_question=$_GET['id_question'];
                            if ($_POST['bool_question']=="on") {
                                $boolean="1";
                            } else {
                                $boolean="0";
                            }
                            // Prépare la requête pour modifier le nom et l'activation de la question puis l'ajoute à la liste des requêtes à exécuter
                            $query='update questions set intitule_question="'.$_POST['intitule_question'].'", bool_question='.$boolean.' where id_question='.$_GET['id_question'].';';
                            array_push($requetes_sql, $query);
                            // Prépare la requête pour supprimer toutes les réponses de la question puis l'ajoute à la liste des requêtes à exécuter
                            $query='delete from reponses where id_question='.$_GET['id_question'].';';
                            array_push($requetes_sql, $query);
                        } else {
                            // Prépare la requête pour créer la question avec le nom et l'activation à oui par défault
                            $query='INSERT INTO questions (intitule_question, bool_question) VALUES ("'.$_POST['intitule_question'].'", "1");';
                            // Éxecute la requête pour obtenir l'id de la question pour ajouter les réponses à la question ensuite
                            $id_question=request($query);
                        }
                        // ATTENTION ! TECHNIQUE !
                        // Met à 1 number qui servira à selectionner les choix
                        $number=1;
                        // Désactive la fin de la boucle WHILE
                        $fin=FALSE;
                        // La boucle WHILE continuera tant que $fin n'est pas égale à TRUE
                        while ($fin==FALSE):
                            if(!isset($_POST['contenu_reponse_'.$number])) {
                                // Si le choix N° $number n'existe pas, ça arrête la boucle
                                $fin=TRUE;
                            } else {
                                // Sinon si le choix existe, ça récupère si c'est une bonne réponse et la convertit pour la requête SQL
                                if ($_POST['bonne_reponse_'.$number]=="on") {
                                    $boolean="1";
                                } else {
                                    $boolean="0";
                                }
                                // Prépare la requête pour ajouter la réponse à la question (avec le contenu de la réponse, si c'est une bonne réponse ou non et l'id de la question) puis l'ajoute à la liste des requêtes à exécuter
                                $query='INSERT INTO reponses (contenu_reponse, bonne_reponse, id_question) VALUES ("'.$_POST['contenu_reponse_'.$number].'", "'.$boolean.'", "'.$id_question.'");';
                                array_push($requetes_sql, $query);
                                // Ajoute 1 pour vérifier l'existance d'autres choix
                                $number++;
                            }
                        endwhile;
                        // Parcours les requêtes à executer
                        foreach ($requetes_sql as $query) {
                            // Execute la requête
                            $requests=request($query);
                        }
                        // Retourne au menu des questions
                        header("Location: .");
                    }
                    
                    // Si c'est une modification, récupère le nom et l'activation de la question
                    if (isset($_GET['id_question'])) {
                        $infos=request('select intitule_question, bool_question from questions where id_question='.$_GET['id_question'].';')[1];
                    }
                    echo '<table id="table_questions" class="border border-black border-2 table table-striped fs-4">';
                        echo '<tr>';
                            echo '<td>Nom Question</td>';
                            echo '<td class="border-start border-end border-black"><input class="form-control" type="text" id="intitule_question" name="intitule_question" required';
                            // Si c'est une modification, affiche le nom de la question
                            if (isset($_GET['id_question'])) {
                                echo ' value="'.$infos[1].'"';
                            }
                            echo '></td>';
                            echo '<td>Réponses</td>';
                        echo '</tr>';
                        // Si c'est une modification, affiche son activation
                        if (isset($_GET['id_question'])) {
                            echo '<tr>';
                                echo '<td class="border-end border-black">Activé</td>';
                                echo '<td>';
                                    echo '<input class="form-check-input" type="checkbox" id="bool_question" name="bool_question"';
                                        // S'il est activé, coche la checkbox
                                        if ($infos[2]==1) {
                                            echo ' checked';
                                        }
                                    echo '/>';
                                echo '</td>';
                                echo '<td></td>';
                            echo '</tr>';
                        }
                
                // Si ce n'est pas une modification, ajoute par default 2 choix
                if (!isset($_GET['id_question'])) {
                    for ($i=1; $i<=2; $i++) {
                        echo '<tr class="choix">';
                            echo '<td>Choix N°'.$i.'</td>';
                            echo '<td class="border-start border-end border-black"><input class="form-control" type="text" id="contenu_reponse_'.$i.'" name="contenu_reponse_'.$i.'" required></td>';
                            echo '<td class="text-center"><input class="form-check-input bg-secondary" type="checkbox" id="bonne_reponse_'.$i.'" name="bonne_reponse_'.$i.'"></td>';
                        echo '</tr>';
                    }
                } else {
                    // Si c'est une modification, récupère les id, les contenus et la vérification de bonne réponse des choix de la question
                    $reponses=request('select id_reponse, contenu_reponse, bonne_reponse from reponses where id_question='.$_GET['id_question'].';');
                    // Parcours les choix
                    for ($i=1; $i<=count($reponses); $i++){
                        echo '<tr class="choix">';
                            // Affiche le numéro du choix
                            echo '<td>Choix N°'.$i.'</td>';
                            // Affiche le contenu du choix
                            echo '<td class="border-start border-end border-black"><input class="form-control" type="text" id="contenu_reponse_'.$i.'" name="contenu_reponse_'.$i.'" value="'.$reponses[$i][2].'" required></td>';
                            echo '<td class="text-center"><input class="form-check-input';
                            // Si c'est une bonne réponse, active la checkbox sinon la met en gris
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
                        // Modifie le nom du bouton selon le mode
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