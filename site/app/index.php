<?php
// Inclure le fichier de connexion
include 'connexion.php';

// Endpoint pour la vérification du login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nom d\'utilisateur ou mot de passe incorrect']);
    }
}

// Endpoint pour récupérer les quizzs faits par l'apprenant
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['apprenant_id'])) {
    $apprenant_id = $_GET['apprenant_id'];

    $sql = "SELECT * FROM quizzs WHERE apprenant_id = $apprenant_id";
    $result = mysqli_query($conn, $sql);

    $quizzs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $quizzs[] = $row;
    }

    echo json_encode($quizzs);
}

// Endpoint pour récupérer les questions d'un quizz
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['quizz_id'])) {
    $quizz_id = $_GET['quizz_id'];

    $sql = "SELECT * FROM questions WHERE quizz_id = $quizz_id";
    $result = mysqli_query($conn, $sql);

    $questions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $questions[] = $row;
    }

    echo json_encode($questions);
}

// Endpoint pour récupérer les résultats par compétences
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['competence'])) {
    $competence = $_GET['competence'];

    $sql = "SELECT * FROM resultats WHERE competence = '$competence'";
    $result = mysqli_query($conn, $sql);

    $resultats = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $resultats[] = $row;
    }

    echo json_encode($resultats);
}

// Fermer la connexion
mysqli_close($conn);
?>
