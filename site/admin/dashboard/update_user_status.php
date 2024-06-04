<?php
session_start();

include '../query.php';
$conn = db_connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST["userId"];
    $status = $_POST["status"];
    
    // Mettre à jour le paramètre bool_user dans la base de données
    $query = "UPDATE utilisateurs SET bool_user = $status WHERE id_user = $userId";
    $conn->query($query);
    
    // Fermer la connexion à la base de données
    $conn->close();
}
?>
