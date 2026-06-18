<?php
// /admin/includes/functions/add_atraccion.php
// Este archivo contiene la lógica para agregar registros a la tabla "atracciones" y para la subida asíncrona de imágenes.

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
// Lógica para agregar un nuevo registro a "atracciones"
// -----------------------------
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $ciudad = $_POST['ciudad'];
    $orden = intval($_POST['orden']);
    $fecha = $_POST['fecha']; // Se espera formato YYYY-MM-DD
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion']; // HTML generado por Quill
    $imagen_url = $_POST['imagen_url'];
    $mapa_url = $_POST['mapa_url'];
    $wikipedia_url = $_POST['wikipedia_url'];
    $instagram_url_1 = $_POST['instagram_url_1'];
    $instagram_url_2 = $_POST['instagram_url_2'];
    $instagram_url_3 = $_POST['instagram_url_3'];
    $visto = isset($_POST['visto']) ? 1 : 0;
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    $stmt = $conn->prepare("INSERT INTO atracciones (ciudad, orden, fecha, nombre, descripcion, imagen_url, mapa_url, wikipedia_url, instagram_url_1, instagram_url_2, instagram_url_3, visto, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssssssssii", $ciudad, $orden, $fecha, $nombre, $descripcion, $imagen_url, $mapa_url, $wikipedia_url, $instagram_url_1, $instagram_url_2, $instagram_url_3, $visto, $activo);
    
    if ($stmt->execute()) {
        header("Location: show-atraccions.php");
        exit;
    } else {
        $error = "Error al agregar registro: " . $stmt->error;
    }
}

$conn->close();
?>
