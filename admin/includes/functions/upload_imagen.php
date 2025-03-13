<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/content/uploads/' . date("Y-m-d") . '/';

    // Crear la carpeta si no existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Extensiones permitidas
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $fileInfo = pathinfo($_FILES['file']['name']);
    $fileExtension = strtolower($fileInfo['extension']);

    if (!in_array($fileExtension, $allowedExtensions)) {
        echo "Formato no permitido";
        exit();
    }

    // Generar nombre aleatorio
    function generateRandomString($length = 10) {
        return strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length));
    }

    $newFileName = generateRandomString() . '.' . $fileExtension;
    $uploadFilePath = $uploadDir . $newFileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath)) {
        echo "success";
    } else {
        echo "Error al mover el archivo";
    }
} else {
    echo "Solicitud inválida";
}
?>
