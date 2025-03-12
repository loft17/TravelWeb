<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';

// Verificar si la imagen se recibe a través de la URL
if (isset($_GET['image'])) {
    $imagePath = $_SERVER['DOCUMENT_ROOT'] . $_GET['image'];

    // Comprobar si el archivo existe
    if (file_exists($imagePath)) {
        // Eliminar la imagen
        unlink($imagePath);

        // Redirigir de vuelta al listado de imágenes
        header('Location: ../../files/show_imgs.php');
        exit();
    } else {
        echo "La imagen no existe.";
    }
} else {
    echo "No se especificó ninguna imagen para eliminar.";
}
?>
