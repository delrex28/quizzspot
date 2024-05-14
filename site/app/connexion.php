<?php

// Définir les variables de connexion
$servername = "localhost";
$username = "web";
$password = "Uslof504";
$dbname = "quizzspot";

// Créer la connexion
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Vérifier la connexion
if (!$conn) {
  die("Échec de la connexion : " . mysqli_connect_error());
}

// Exécuter une requête (exemple)
$sql = "SELECT * FROM utilisateurs";
$result = mysqli_query($conn, $sql);

// Fermer la connexion
mysqli_close($conn);

?>