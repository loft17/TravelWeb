<?php
// /admin/includes/functions/edit_atraccion.php

include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();

// Migración automática: añadir lat/lng si no existen
if (!isset($_SESSION['_migration_coords'])) {
    $chk = $conn->query("SHOW COLUMNS FROM atracciones LIKE 'lat'");
    if ($chk && $chk->num_rows === 0) {
        $conn->query("ALTER TABLE atracciones ADD COLUMN lat DECIMAL(10,8) DEFAULT NULL");
        $conn->query("ALTER TABLE atracciones ADD COLUMN lng DECIMAL(11,8) DEFAULT NULL");
    }
    $_SESSION['_migration_coords'] = true;
}

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
// Lógica para editar un registro en "atracciones"
// -----------------------------

if (!isset($_GET['id'])) {
    echo "ID no especificado.";
    exit;
}
$id = intval($_GET['id']);

$error = "";
// Procesar el formulario al enviarse (método POST)
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
    $visto  = isset($_POST['visto'])  ? 1 : 0;
    $activo = isset($_POST['activo']) ? 1 : 0;
    $lat    = ($_POST['lat'] ?? '') !== '' ? floatval($_POST['lat']) : null;
    $lng    = ($_POST['lng'] ?? '') !== '' ? floatval($_POST['lng']) : null;

    $stmt = $conn->prepare("UPDATE atracciones SET ciudad = ?, orden = ?, fecha = ?, nombre = ?, descripcion = ?, imagen_url = ?, mapa_url = ?, wikipedia_url = ?, instagram_url_1 = ?, instagram_url_2 = ?, instagram_url_3 = ?, visto = ?, activo = ?, lat = ?, lng = ? WHERE id = ?");
    $stmt->bind_param("sisssssssssiiddi", $ciudad, $orden, $fecha, $nombre, $descripcion, $imagen_url, $mapa_url, $wikipedia_url, $instagram_url_1, $instagram_url_2, $instagram_url_3, $visto, $activo, $lat, $lng, $id);

    if ($stmt->execute()) {
        header("Location: /admin/pages/atracciones/show-atraccions.php");
        exit;
    } else {
        $error = "Error al actualizar registro: " . $stmt->error;
    }
}

// Consultar el registro a editar
$stmt = $conn->prepare("SELECT * FROM atracciones WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Registro no encontrado.";
    exit;
}
$atraccion = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
