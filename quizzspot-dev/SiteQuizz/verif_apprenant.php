<?php
// Récupérer le nom de l'apprenant depuis les paramètres GET
$nomcomplet = isset($_GET['nomcomplet']) ? $_GET['nomcomplet'] : '';

// Liste des noms autorisés
$nomsAutorises = array("Sherlock Holmes", "Thierry Testo");

// Vérifier si le nom sélectionné est autorisé
if (in_array($nomcomplet, $nomsAutorises)) {
    // Le nom est autorisé, renvoyer "true"
    $response = array("result" => "true");
} else {
    // Le nom n'est pas autorisé, renvoyer "false"
    $response = array("result" => "false");
}

// Définir l'en-tête de contenu comme JSON
header('Content-Type: application/json');

// Envoyer la réponse JSON
echo json_encode($response);
?>
