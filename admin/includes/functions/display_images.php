<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';

// Función para mostrar las imágenes
function displayImages($images) {
    $baseUrl = "http://" . $_SERVER['HTTP_HOST']; // Obtener la URL base con http://

    echo '<div style="display: flex; flex-wrap: wrap;">';
    foreach ($images as $image) {
        $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $image); // Ruta relativa a la carpeta web
        $imageUrl = $baseUrl . $relativePath; // Crear la URL completa con el dominio

        echo '<div style="margin: 10px; width: 200px; text-align: center; position: relative;">'; // Añadir position: relative al contenedor
        echo '<img src="' . $imageUrl . '" alt="Image" style="width: 100%; max-width: 180px; height: auto;" onclick="copyImageUrl(\'' . $imageUrl . '\');"/>';
        echo '<p>' . basename($image) . '</p>';
        
        // Agregar el botón de papelera en la esquina superior derecha con recuadro blanco y 75% de transparencia
        echo '<a href="../includes/functions/delete_image.php?image=' . urlencode(str_replace($_SERVER['DOCUMENT_ROOT'], '', $image)) . '" onclick="return confirm(\'¿Estás seguro de que quieres eliminar esta imagen?\');" style="position: absolute; top: 3px; right: 13px; background: rgba(255, 255, 255, 0.75); padding: 5px; display: inline-block;">';
        echo '<button style="background: none; border: none; cursor: pointer; color: red; font-size: 20px;">';
        echo '<i class="fas fa-trash"></i>'; // Icono de Font Awesome para la papelera
        echo '</button>';
        echo '</a>';
        echo '</div>';
    }
    echo '</div>';

    // Incluir el script JavaScript externo

}
?>
