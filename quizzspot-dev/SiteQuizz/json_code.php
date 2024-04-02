<?php
//En attendant l'API, ce php renvoie un json dans le format attendu après avoir entré un code de participation
$code = $_GET['code'];
if ($code == "4242") {
    $test = array(
        'code' => true
    );
    
    $participants = array(
        array('id' => 1, 'nom' => 'Holmes', 'prenom' => 'Sherlock'),
        array('id' => 2, 'nom' => 'Testo', 'prenom' => 'Thierry'),
        array('id' => 3, 'nom' => 'Ponce', 'prenom' => 'Jean-Pierre')
    );
    
    // Compter le nombre de participants
    $nombreParticipants = count($participants);
    
    // Fusionner les données
    $merge = array(
        'test' => $test,
        'nombre_participants' => $nombreParticipants,
        'participants' => $participants
    );
    $json = json_encode($merge);
    // Définir les en-têtes HTTP pour indiquer que la réponse est un fichier JSON
    header('Content-Type: application/json');

    echo $json;
} else {
    $erreur = array(
        'code' => False,

    );
    $json = json_encode($erreur);
    header('Content-Type: application/json');

    echo $json;
}
?>
