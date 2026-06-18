<?php

define('TITLE_WEB', 'Thai25');
define('URL_WEB', 'http://192.168.0.118');

define('DB_HOST', 'localhost');
define('DB_USER', 'travel_user');
define('DB_PASS', 'pruebas'); // Cambia por tu contraseña
define('DB_NAME', 'travel_db');

// Función para conectar a la base de datos
function conectar_bd() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        error_log("DB connection error: " . $conn->connect_error);
        die("Error de conexión con la base de datos.");
    }

    return $conn;
}
?>
