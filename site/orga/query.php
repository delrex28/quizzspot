<?php
function db_connect() {
    $servername = "localhost";
    $username = "web";
    $password = "Uslof504";
    $dbname = "quizzspot";

    // Créer une connexion
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>
