<?php
// /admin/includes/functions/edit_atraccion.php

include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();

// -----------------------------
// Subida asíncrona de imagen (para llamadas AJAX)
// -----------------------------
if (isset($_GET['action']) && $_GET['action'] === 'upload_image') {

    // Función para generar un string aleatorio de 10 caracteres (mayúsculas y números)
    function generateRandomString($length = 10) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    if (isset($_FILES['imagen_file']) && $_FILES['imagen_file']['error'] == 0) {
        $allowedTypes = array('image/jpeg', 'image/png', 'image/webp');
        $fileType = $_FILES['imagen_file']['type'];
        if (!in_array($fileType, $allowedTypes)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Tipo de archivo no permitido. Solo se permiten JPG, PNG y WEBP.']);
            exit;
        }
        $extension = '';
        switch ($fileType) {
            case 'image/jpeg': $extension = '.jpg'; break;
            case 'image/png': $extension = '.png'; break;
            case 'image/webp': $extension = '.webp'; break;
        }
        // Carpeta de destino: /content/uploads/YYYY-MM-DD/
        $uploadDirBase = $_SERVER['DOCUMENT_ROOT'] . '/content/uploads/';
        $today = date("Y-m-d");
        $targetDir = $uploadDirBase . $today . "/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        // Generar nombre aleatorio de 10 caracteres
        $newFilename = generateRandomString(10) . $extension;
        $targetFile = $targetDir . $newFilename;
        if (move_uploaded_file($_FILES['imagen_file']['tmp_name'], $targetFile)) {
            $imagen_url = '/content/uploads/' . $today . '/' . $newFilename;
            header('Content-Type: application/json');
            echo json_encode(['imagen_url' => $imagen_url]);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al subir la imagen.']);
            exit;
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No se subió ninguna imagen o se produjo un error.']);
        exit;
    }
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

    $stmt = $conn->prepare("UPDATE atracciones SET ciudad = ?, orden = ?, fecha = ?, nombre = ?, descripcion = ?, imagen_url = ?, mapa_url = ?, wikipedia_url = ?, instagram_url_1 = ?, instagram_url_2 = ?, instagram_url_3 = ?, visto = ?, activo = ? WHERE id = ?");
    $stmt->bind_param("sisssssssssiii", $ciudad, $orden, $fecha, $nombre, $descripcion, $imagen_url, $mapa_url, $wikipedia_url, $instagram_url_1, $instagram_url_2, $instagram_url_3, $visto, $activo, $id);

    if ($stmt->execute()) {
        header("Location: /admin/pages/atracciones/show-atraccion.php");
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
