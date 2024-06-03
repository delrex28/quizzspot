<?php
function db_connect() {
    $servername = 'localhost';
    $username = 'root';
    $password = 'Uslof504';
    $db_name = 'quizzspot';

    $driver = new mysqli_driver();
    $driver->report_mode = MYSQLI_REPORT_STRICT;
    try {
        $conn = new mysqli($servername, $username, $password, $db_name);
        return $conn;
    } catch (mysqli_sql_exception $e) {
        echo "Connexion impossible " . $e->__toString();
        exit;
    }
}
?>