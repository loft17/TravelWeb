<?php

defined('TITLE_WEB') || define('TITLE_WEB', 'Thai25');
defined('URL_WEB')   || define('URL_WEB',   'http://192.168.0.118');
defined('VIAJE_ID')  || define('VIAJE_ID',  1); // ID del viaje a mostrar en la web pública

defined('DB_HOST') || define('DB_HOST', 'localhost');
defined('DB_USER') || define('DB_USER', 'travel_user');
defined('DB_PASS') || define('DB_PASS', 'pruebas');
defined('DB_NAME') || define('DB_NAME', 'travel_db');

if (!function_exists('conectar_bd')) {
    function conectar_bd(): mysqli {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            error_log("DB connection error: " . $conn->connect_error);
            die("Error de conexión con la base de datos.");
        }
        return $conn;
    }
}
?>
