<?php
session_start();

include '../../query.php';
$conn = db_connect();

if (!$conn) {
    die("Échec de la connexion à la base de données: " . mysqli_connect_error());
}

if (!isset($_SESSION["user"])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_quizz = $_POST["id_quizz"];
    $new_status = $_POST["new_status"];

    $query = "UPDATE quizzs SET bool_quizz = ? WHERE id_quizz = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ii", $new_status, $id_quizz);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Le statut du quizz a été mis à jour']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour du quizz']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur de préparation de la requête']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Méthode de requête invalide']);
}

$conn->close();
?>
