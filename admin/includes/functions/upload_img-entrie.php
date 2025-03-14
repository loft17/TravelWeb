<?php
// -----------------------------
// Sección: Subida asíncrona de imagen
// -----------------------------
if (isset($_GET['action']) && $_GET['action'] === 'upload_image') {
    include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

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
            // Se construye la URL relativa para guardar en la base de datos
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
?>