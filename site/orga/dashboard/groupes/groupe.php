<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Paramètres Groupe</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script>
			function retour(){
				window.location.href = ".";
			}

            // ATTENTION ! TECHNIQUE !
            // Dans les select, ils ont un paramètre "ondblclick", le premier a le mode "top" et le seconde "bottom", en gros, quand un apprenant est double clické, cette fonction se lance
            function change(mode){
                // Récupère le premier select
                select_top=document.querySelector("#id_user_top");
                // Récupère le second select
                select_bottom=document.querySelector("#id_user_bottom");
                // Récupère l'objet invisble
                hidden=document.querySelector("#id_users");

                if (mode=="top") {
                    // Si c'est un double click dans le premier select, récupère les id et les tranforme en liste puis enlève le vide rajouté lors de la création de l'objet invisble
                    user=hidden.value.split(",")
                    userLimitee = user.slice(0, user.length - 1);
                    // Si le nombre d'id est inférieur à 30
                    if (userLimitee.length<30) {
                        // Récupère l'apprenant double clické dans le premier select
                        option=select_top.selectedOptions[0];
                        // Rajoute à l'object invisible, l'id de l'apprenant
                        hidden.value+=option.value+",";
                        // Rajoute au second select, l'apprenant double clické dans celui du dessus
                        select_bottom.appendChild(option);
                        // Enlève l'apprenant double clické dans le premier select
                        select_top.removeChild(option);
                    }
                } else {
                    // Si c'est un double click dans le second select, récupère l'apprenant double clické dans le second select
                    option=select_bottom.selectedOptions[0];
                    // Récupère le string des id des apprenants dans l'objet invisible
                    valeurActuelle = hidden.value;
                    valeurARetirer = option.value+",";
                    // Vérification si la valeur à retirer est présente dans la valeur actuelle
                    var index = valeurActuelle.indexOf(valeurARetirer);
                    if (index !== -1) {
                        // Retrait de la valeur de la chaîne
                        var nouvelleValeur = valeurActuelle.substring(0, index) + valeurActuelle.substring(index + valeurARetirer.length);
                        // Mise à jour de la valeur du champ d'entrée caché
                        hidden.value = nouvelleValeur;
                    }
                    // Rajoute au premier select, l'apprenant double clické dans celui du dessous
                    select_top.appendChild(option);
                    // Sélectionne le premier apprenant du second select
                    select_bottom.selectedIndex=0;
                    // Enlève l'apprenant double clické dans le second select
                    select_bottom.removeChild(option);
                }
			}

		</script>
    </head>

    <body>
        <div class="row m-4">
			<img class="col-auto" src="../retour.png" alt="Retour" style="width:5%;" onclick="retour()">
			<div class="col row justify-content-center">
                <?php
                echo '<h3 class="col-auto align-self-center" style="margin-right:7%;">Page';
                // Vérifie dans l'url si il y a le paramètre id_groupe, s'il y est c'est une modification sinon une création
                if (isset($_GET['id_groupe'])) {
                    echo ' Modification ';
                } else {
                    echo ' Création ';
                }
                echo 'Groupe</h3>';
				?>
			</div>
		</div>
		<div class="mt-5 container">
            <div class="row justify-content-center">
                <form action="" method="post" class="col-auto">
                    <?php
                    // Appelle l'api PHP
                    require_once "../query.php";
					$list_id_apprenants=[];
                    // Si c'est une modification, récupère le nom du groupe et son activation, récupère l'id et le "nom prénom" des apprenants dans le groupe
                    if (isset($_GET['id_groupe'])) {
                        $infos_groupe=request("SELECT nom_groupe, bool_groupe FROM groupes where id_groupe=".$_GET['id_groupe'].";")[1];
                        $apprenants=request("SELECT rel_utilisateurs_groupes.id_user, concat(utilisateurs.nom_user, ' ', utilisateurs.prenom_user) FROM rel_utilisateurs_groupes inner join utilisateurs on utilisateurs.id_user=rel_utilisateurs_groupes.id_user where rel_utilisateurs_groupes.id_groupe=".$_GET['id_groupe'].";");
                        // Créé une liste avec juste l'id des apprenants dans le groupe pour des vérifications plus tard, parcours des apprenants
                        foreach ($apprenants as $apprenant) {
                            // Ajoute dans la liste list_id_apprenants, l'id de l'apprenants
                            array_push($list_id_apprenants, $apprenant[1]);
                        }
                        // Ajoute un vide à la liste pour éviter des erreurs plus tard
                        array_push($list_id_apprenants, "");
                    }
                    // Créé un objet invisible pour le JavaScript avec la liste des id des apprenants selectionnés
                    echo '<input type="hidden" id="id_users" name="id_users"';
                    // Si c'est une modification, ajoute la liste des id des apprenants dans le groupe
                    if (isset($_GET['id_groupe'])) {
                        // Implode sépare une liste [1,5,6,7] en string "1,5,6,7"
                        echo ' value="'.implode(",", $list_id_apprenants).'"';
                    }
                    echo '/>';
                    echo '<table class="border border-black border-2 table table-striped fs-4">';
                        echo '<tr>';
                            echo '<td class="border-end border-black">Nom Groupe</td>';
                            echo '<td><input class="form-control" type="text" id="nom_groupe" name="nom_groupe" required';
                            // Si c'est une modification, affiche le nom du groupe
                            if (isset($_GET['id_groupe'])) {
                                echo ' value="'.$infos_groupe[1].'"';
                            }
                            echo '/></td>';
                        echo '</tr>';
                        // ATTENTION ! TECHNIQUE !
                        // Selection des apprenants à ajouter dans le groupe
                        echo '<tr>';
                            echo '<td class="border-end border-black">Apprenants</td>';
                            echo '<td>';
                                echo '<select class="form-select" id="id_user_top" multiple ondblclick="change(`top`)">';
                                    // Récupère l'id, le "nom prénom" des utilisateur avec le rôle "apprenants"
                                    $infos=request("SELECT id_user, concat(nom_user, ' ', prenom_user) FROM utilisateurs where bool_user=1 and role_user='apprenants';");
                                    // Parcours les apprenants
                                    foreach ($infos as $info){
                                        // Si l'id de l'apprenant n'est pas dans la liste list_id_apprenants (liste des id des apprenants déjà dans le groupe) alors on l'ajoute (on ne va pas permettre de rajouter un apprenant dans le groupe s'il y est déjà)
                                        if (in_array($info[1], $list_id_apprenants)==FALSE) {
                                            echo '<option value="'.$info[1].'">'.$info[2].'</option>';
                                        }
                                    }
                                echo '</select>';
                            echo '</td>';
                        echo '</tr>';
                        // Selection des apprenants déjà dans le groupe
                        echo '<tr>';
                            echo '<td class="border-end border-black">Dans le Groupe</td>';
                            echo '<td>';
                                echo '<select class="form-select" id="id_user_bottom" multiple required ondblclick="change(`bottom`)">';
                                // Exactement pareil qu'au dessus mais cette fois-ci on vérifie si l'id de l'apprenant est dans la liste des apprenants dans le groupe
                                foreach ($apprenants as $apprenant){
                                    if (in_array($apprenant[1], $list_id_apprenants)==TRUE) {
                                        echo '<option value="'.$apprenant[1].'">'.$apprenant[2].'</option>';
                                    }
                                }
                                echo '</select>';
                            echo '</td>';
                        echo '</tr>';
                        // Si c'est une modification, affiche son activation
                        if (isset($_GET['id_groupe'])) {
                            echo '<tr>';
                                echo '<td class="border-end border-black">Activé</td>';
                                echo '<td>';
                                    echo '<input class="form-check-input" type="checkbox" id="bool_groupe" name="bool_groupe"';
                                    // S'il est activé, coche la checkbox    
                                    if ($infos_groupe[2]==1) {
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
                        if (isset($_GET['id_groupe'])) {
                            echo 'Modifier';
                        } else {
                            echo 'Créer';
                        }  
                        echo '</button>';
                    echo '</div>';
                echo '</form>';
                
                // ATTENTION ! TECHNIQUE !
                // Si la page a été soumise (le bouton avec le type "submit" a été clické)
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $requetes_sql=[];
                    if (isset($_GET['id_groupe'])) {
                        // Si c'est une modification, récupère son activation et la convertit pour la requête SQL
                        if ($_POST['bool_groupe']=="on") {
                            $boolean="1";
                        } else {
                            $boolean="0";
                        }
                        // Prépare la requête pour modifier le nom et l'activation du groupe puis l'ajoute à la liste des requêtes à exécuter
                        $query='update groupes set nom_groupe="'.$_POST['nom_groupe'].'", bool_groupe='.$boolean.' where id_groupe='.$_GET['id_groupe'].';';
                        array_push($requetes_sql, $query);
                        // Prépare la requête pour supprimer tout les apprenants dans le groupe puis l'ajoute à la liste des requêtes à exécuter
                        $query='delete from rel_utilisateurs_groupes where id_groupe='.$_GET['id_groupe'].';';
                        array_push($requetes_sql, $query);
                        // Transforme le string des id des apprenants (ligne 82 dans ce code) en liste
                        $ids=explode(",", $_POST['id_users']);
                        // Enlève le vide rajouté au début
                        $ids_limite = array_slice($ids, 0, count($ids) - 1);
                        // Prépare la requête pour ajouter tout les apprenants dans le groupe
                        $query='insert into rel_utilisateurs_groupes(id_user, id_groupe) values ';
                        // Parcours la liste des apprenants
                        foreach ($ids_limite as $id) {
                            // Ajoute à la requête l'id de l'apprenant avec celui du groupe
                            $query.='('.$id.','.$_GET['id_groupe'].'),';
                        }
                        // Enlève la dernière , dans la requête sinon y a une erreur SQL
                        $query = substr($query, 0, -1);
                        // Rajoute la fermeture de la requête puis l'ajoute à la liste des requêtes à exécuter
                        $query.=";";
                        array_push($requetes_sql, $query);
                    } else {
                        // Prépare la requête pour créer le groupe avec le nom et l'activation à oui par défault
                        $query='insert into groupes(nom_groupe, bool_groupe) values ("'.$_POST['nom_groupe'].'",1);';
                        // Éxecute la requête pour obtenir l'id du groupe pour ajouter les apprenants dans le groupe ensuite
                        $id_groupe=request($query);
                        // Transforme le string des id des apprenants (ligne 82 dans ce code) en liste
                        $ids=explode(",", $_POST['id_users']);
                        // Enlève le vide rajouté au début
                        $ids_limite = array_slice($ids, 0, count($ids) - 1);
                        // Prépare la requête pour ajouter tout les apprenants dans le groupe
                        $query='insert into rel_utilisateurs_groupes(id_user, id_groupe) values ';
                        // Parcours la liste des apprenants
                        foreach ($ids_limite as $id) {
                            // Ajoute à la requête l'id de l'apprenant avec celui du groupe précédemment obtenu
                            $query.='('.$id.','.$id_groupe.'),';
                        }
                        // Enlève la dernière , dans la requête sinon y a une erreur SQL
                        $query = substr($query, 0, -1);
                        // Rajoute la fermeture de la requête puis l'ajoute à la liste des requêtes à exécuter
                        $query.=";";
                        array_push($requetes_sql, $query);
                    }
                    // Parcours les requêtes à exécuter
                    foreach ($requetes_sql as $query) {
                        // Execute la requête
                        $requests=request($query);
                    }
                    // Retourne au menu des groupes
                    header("Location: .");
                }
                ?>
            </div>
		</div>
    </body>
</html>