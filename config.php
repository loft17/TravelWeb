<?php
// Definir la ruta base de tu proyecto (ajusta según tu estructura de directorios)
define('URL_WEB', 'http://192.168.0.118');

define('DB_HOST', 'localhost');
define('DB_USER', 'travel_user');
define('DB_PASS', 'pruebas'); // Cambia por tu contraseña
define('DB_NAME', 'travel_db');

// Función para conectar a la base de datos
function conectar_bd() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar si hay algún error en la conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    return $conn;  // Devolver la conexión
}
?>
