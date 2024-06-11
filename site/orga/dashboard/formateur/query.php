<?php
function request($query){
    $servername = 'localhost';
    $username = 'web';
    $password = 'Uslof504';
    $db_name = 'quizzspot';

	$driver = new mysqli_driver();
	$driver->report_mode = MYSQLI_REPORT_STRICT;
	try {
		$lower=strtolower($query);

    	$conn = new mysqli($servername,$username,$password,$db_name);
        $query = $conn->prepare($query);
        $query->execute();
		$result = $query->get_result();

		$mode=explode(" ",$lower)[0];
		if ($mode=="select") {
			$json = [];
			foreach ($result as $key => $subArray) {
				$innerJson = [];
				$i=1;
				foreach ($subArray as $innerKey => $value) {
					$innerJson[$i] = $value;
					$i++;
				}
				$json[$key + 1] = $innerJson;
			}
			$json_output = json_encode($json);
			$json_decode=json_decode($json_output, true);
			return $json_decode;
		} elseif ($mode == "insert") {
            // Retourne l'ID de l'insertion
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