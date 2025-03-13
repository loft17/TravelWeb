<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['image'])) {
    $imagePath = $_SERVER['DOCUMENT_ROOT'] . $_POST['image'];

    // Validar que el archivo existe
    if (file_exists($imagePath)) {
        if (unlink($imagePath)) {
            echo "success"; // Imagen eliminada correctamente
        } else {
            echo "error"; // No se pudo eliminar
        }
    } else {
        echo "error"; // Archivo no encontrado
    }
} else {
    echo "error"; // Petición inválida
}
?>
