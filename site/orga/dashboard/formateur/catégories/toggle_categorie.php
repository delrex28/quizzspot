<?php
session_start();

include '../../query.php';
$conn = db_connect();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: ../index.php"); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_categorie = $_POST["id_categorie"];
    $new_status = $_POST["new_status"];
    $query = "UPDATE categories SET bool_categorie = ? WHERE id_categorie = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $new_status, $id_categorie);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
}
?>
