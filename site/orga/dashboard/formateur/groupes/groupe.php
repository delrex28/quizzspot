<?php
session_start();
include '../query.php';

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
    <title>Paramètres Groupe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script>
        function retour(){
            window.location.href = ".";
        }

        function change(mode){
            select_top=document.querySelector("#id_user_top");
            select_bottom=document.querySelector("#id_user_bottom");
            hidden=document.querySelector("#id_users");

            if (mode=="top") {
                user=hidden.value.split(",")
                userLimitee = user.slice(0, user.length - 1);
                if (userLimitee.length<30) {
                    option=select_top.selectedOptions[0];
                    hidden.value+=option.value+",";
                    select_bottom.appendChild(option);
                    select_top.removeChild(option);
                }
            } else {
                option=select_bottom.selectedOptions[0];
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
                select_top.appendChild(option);
                select_bottom.selectedIndex=0;
                select_bottom.removeChild(option);
            }
        }
    </script>
</head>

<body class="" style="">
    <div class="row m-4">
        <img class="col-auto" src="../../img/retour.png" alt="Retour" style="width:5%;" onclick="retour()">
        <div class="col row justify-content-center">
            <?php
            echo '<h3 class="col-auto align-self-center" style="margin-right:7%;">Page';
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
                require_once "../query.php";
                $list_id_apprenants=[];
                if (isset($_GET['id_groupe'])) {
                    $infos_groupe=request("SELECT nom_groupe, bool_groupe FROM groupes where id_groupe=".$_GET['id_groupe'].";")[1];
                    $apprenants=request("SELECT rel_utilisateurs_groupes.id_user, concat(utilisateurs.nom_user, ' ', utilisateurs.prenom_user) FROM rel_utilisateurs_groupes inner join utilisateurs on utilisateurs.id_user=rel_utilisateurs_groupes.id_user where rel_utilisateurs_groupes.id_groupe=".$_GET['id_groupe'].";");
                    foreach ($apprenants as $apprenant) {
                        array_push($list_id_apprenants, $apprenant[1]);
                    }
                    array_push($list_id_apprenants, "");
                }
                echo '<input type="hidden" id="id_users" name="id_users"';
                if (isset($_GET['id_groupe'])) {
                    echo ' value="'.implode(",", $list_id_apprenants).'"';
                }
                echo '/>';
                echo '<table class="border border-black border-2 table table-striped fs-4">';
                echo '<tr>';
                echo '<td class="border-end border-black">Nom Groupe</td>';
                echo '<td><input class="form-control" type="text" id="nom_groupe" name="nom_groupe" required';
                if (isset($_GET['id_groupe'])) {
                    echo ' value="'.$infos_groupe[1].'"';
                }
                echo '/></td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td class="border-end border-black">Apprenants</td>';
                echo '<td>';
                echo '<select class="form-select" id="id_user_top" multiple ondblclick="change(`top`)">';
                $infos=request("SELECT id_user, concat(nom_user, ' ', prenom_user) FROM utilisateurs where bool_user=1 and role_user=1;"); // Modification du rôle utilisateur ici
                foreach ($infos as $info){
                    if (in_array($info[1], $list_id_apprenants)==FALSE) {
                        echo '<option value="'.$info[1].'">'.$info[2].'</option>';
                    }
                }
                echo '</select>';
                echo '</td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td class="border-end border-black">Dans le Groupe</td>';
                echo '<td>';
                echo '<select class="form-select" id="id_user_bottom" multiple required ondblclick="change(`bottom`)">';
                foreach ($apprenants as $apprenant){
                    if (in_array($apprenant[1], $list_id_apprenants)==TRUE) {
                        echo '<option value="'.$apprenant[1].'">'.$apprenant[2].'</option>';
                    }
                }
                echo '</select>';
                echo '</td>';
                echo '</tr>';
                if (isset($_GET['id_groupe'])) {
                    echo '<tr>';
                    echo '<td class="border-end border-black">Activé</td>';
                    echo '<td>';
                    echo '<input class="form-check-input" type="checkbox" id="bool_groupe" name="bool_groupe"';
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
                if (isset($_GET['id_groupe'])) {
                    echo 'Modifier';
                } else {
                    echo 'Créer';
                }  
                echo '</button>';
                echo '</div>';
                echo '</form>';
                
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $requetes_sql=[];
                    if (isset($_GET['id_groupe'])) {
                        if ($_POST['bool_groupe']=="on") {
                            $boolean="1";
                        } else {
                            $boolean="0";
                        }
                        $query='update groupes set nom_groupe="'.$_POST['nom_groupe'].'", bool_groupe='.$boolean.' where id_groupe='.$_GET['id_groupe'].';';
                        array_push($requetes_sql, $query);
                        $query='delete from rel_utilisateurs_groupes where id_groupe='.$_GET['id_groupe'].';';
                        array_push($requetes_sql, $query);
                        $ids=explode(",", $_POST['id_users']);
                        $ids_limite = array_slice($ids, 0, count($ids) - 1);
                        $query='insert into rel_utilisateurs_groupes(id_user, id_groupe) values ';
                        foreach ($ids_limite as $id) {
                            $query.='('.$id.','.$_GET['id_groupe'].'),';
                        }
                        $query = substr($query, 0, -1);
                        $query.=";";
                        array_push($requetes_sql, $query);
                    } else {
                        $query='insert into groupes(nom_groupe, bool_groupe) values ("'.$_POST['nom_groupe'].'",1);';
                        $id_groupe=request($query);
                        $ids=explode(",", $_POST['id_users']);
                        $ids_limite = array_slice($ids, 0, count($ids) - 1);
                        $query='insert into rel_utilisateurs_groupes(id_user, id_groupe) values ';
                        foreach ($ids_limite as $id) {
                            $query.='('.$id.','.$id_groupe.'),';
                        }
                        $query = substr($query, 0, -1);
                        $query.=";";
                        array_push($requetes_sql, $query);
                    }

                    foreach ($requetes_sql as $query) {
                        $requests=request($query);
                    }
                    header("Location: .");
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
