<?php
function request($query){
    // Compte de la BDD
    $servername = 'localhost';
    $username = 'web';
    $password = 'Uslof504';
    $db_name = 'quizzspot';

	$driver = new mysqli_driver();
	$driver->report_mode = MYSQLI_REPORT_STRICT;
	try {
        // Récupère la requête et la met en minuscule
		$lower=strtolower($query);

        // Créé une connection, prépare la requête, l'éxecute et retourne le résultat
        $conn = new mysqli($servername,$username,$password,$db_name);
        $query = $conn->prepare($query);
        $query->execute();
		$result = $query->get_result();

        // Récupère la méthode (select, insert, update) dans la requête mise en minuscule
		$mode=explode(" ",$lower)[0];
		if ($mode=="select") {
            // Si c'est un select, ça récupère le tableau de résultats et prépare JSON
			$json = [];
            // Parcours les lignes du tableau
			foreach ($result as $key => $subArray) {
                // Créé une ligne pour le JSON et prépare les indices pour accéder aux infos
				$innerJson = [];
				$i=1;
                // Parcours les colonnes d'une ligne
				foreach ($subArray as $innerKey => $value) {
                    // Met à l'indice $i la valeur de la colonne dans la ligne JSON et ajoute 1 à l'indice
					$innerJson[$i] = $value;
					$i++;
				}
                // Ajoute la ligne JSON au JSON principal
				$json[$key + 1] = $innerJson;
			}
            // Encode le JSON pour simuler l'api
			$json_output = json_encode($json);
            // Le décode pour simuler la récupération du JSON de l'api puis le retourne
			$json_decode=json_decode($json_output, true);
			return $json_decode;
		} elseif ($mode == "insert") {
            // Si c'est un insert, retourne son ID
            return $conn->insert_id;
        }
		return "query_ok";
	}
	catch (mysqli_sql_exception $e ) {
		echo "Connexion impossible ".$e->__toString();
		exit;
	}
}
?>