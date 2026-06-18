<?php
// /admin/includes/functions/add_food.php
// Este archivo contiene la lógica para agregar registros a la tabla "comida" y para la subida asíncrona de imágenes.

include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();

// -----------------------------
// Subida asíncrona de imagen (para llamadas AJAX)
// -----------------------------
if (isset($_GET['action']) && $_GET['action'] === 'upload_image') {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/helpers.php';
    header('Content-Type: application/json');
    echo json_encode(uploadImageFile('imagen_file'));
    exit;
}

// -----------------------------
// Lógica para agregar un nuevo registro
// -----------------------------
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion']; // HTML generado por Quill
    $puntuacion = isset($_POST['puntuacion']) ? intval($_POST['puntuacion']) : 0;
    // Limitar la puntuación entre 0 y 5
    if ($puntuacion < 0) $puntuacion = 0;
    if ($puntuacion > 5) $puntuacion = 5;
    
    // Se utiliza la URL que venga en el campo (ya sea la ingresada o actualizada por AJAX)
    $imagen_url = $_POST['imagen_url'];
    $comido = isset($_POST['comido']) ? 1 : 0;
    
    $stmt = $conn->prepare("INSERT INTO comida (nombre, descripcion, puntuacion, imagen_url, comido) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisi", $nombre, $descripcion, $puntuacion, $imagen_url, $comido);
    
    if ($stmt->execute()) {
        header("Location: show-foods.php");
        exit;
    } else {
        $error = "Error al agregar registro: " . $stmt->error;
    }
}

$conn->close();
?>
