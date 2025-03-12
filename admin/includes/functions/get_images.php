<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';

// Función recursiva para obtener imágenes
function getImagesFromDirectory($dir) {
    $images = [];
    // Escanear el contenido del directorio
    $files = scandir($dir);
    
    foreach ($files as $file) {
        // Ignorar los directorios '.' y '..'
        if ($file === '.' || $file === '..') {
            continue;
        }

        // Obtener la ruta completa del archivo
        $filePath = $dir . '/' . $file;

        // Si es un directorio, llamar a la función recursivamente
        if (is_dir($filePath)) {
            $images = array_merge($images, getImagesFromDirectory($filePath));
        } else {
            // Comprobar si el archivo es una imagen (por extensión)
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
                $images[] = $filePath;
            }
        }
    }
    
    return $images;
}

// Obtener las imágenes desde el directorio
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/content/uploads/';
$images = getImagesFromDirectory($rootDir);

return $images;
?>
