<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';

// Incluir el archivo de configuración para acceder a las constantes
include_once '../../config.php';

try {
    // Establecer conexión con la base de datos utilizando las constantes definidas en config.php
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Configuramos PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consulta para obtener los usuarios
    $query = "SELECT id, name, email, rol, date_reg, active FROM users";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    // Obtener todos los usuarios
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En caso de error con la conexión o la consulta
    echo "Error al conectar o realizar la consulta: " . $e->getMessage();
    exit;
}
?>
