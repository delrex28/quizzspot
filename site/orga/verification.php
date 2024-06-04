<?php
session_start();
include 'query.php'; // Inclure le fichier contenant la fonction db_connect()

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"]) && isset($_POST["password"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
        $email = $_POST["email"];
        $password = sha1($_POST["password"]); // Hash du mot de passe avec SHA-1

        $query = "SELECT * FROM utilisateurs WHERE email_user = ?";
        $user = verify_credentials($query, "s", $email);

        if ($user && $password == $user['mdp_user']) {
            if ($user['role_user'] === 'administrateur' || $user['role_user'] === 'formateur') {
                $_SESSION["user"] = $user; // Stocke les informations de l'utilisateur dans la session
                header("Location: accueil.php");
                exit();
            } elseif ($user['role_user'] === 'apprenant') {
                $error = "Vous n'avez pas accès à cette page.";
            } else {
                $error = "Rôle utilisateur non reconnu.";
            }
        } else {
            $error = "Identifiants incorrects. Veuillez réessayer.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

function verify_credentials($query, ...$params) {
    $conn = db_connect(); // Appelle la fonction pour établir la connexion à la base de données
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Erreur de préparation de la requête: " . $conn->error);
    }
    if ($stmt->bind_param(...$params) === false) {
        die("Erreur lors de la liaison des paramètres: " . $stmt->error);
    }
    if ($stmt->execute() === false) {
        die("Erreur lors de l'exécution de la requête: " . $stmt->error);
    }
    $result = $stmt->get_result();
    if ($result === false) {
        die("Erreur lors de la récupération des résultats: " . $stmt->error);
    }
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $user;
}
?>
