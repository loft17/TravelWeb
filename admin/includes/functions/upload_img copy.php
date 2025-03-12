<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';

function procesarSubidaImagen($file) {
    // Definir la ruta donde se guardarán las imágenes, que será en una carpeta con la fecha actual
    $fecha = date("Y-m-d");
    $directorio = "../../content/uploads/" . $fecha;

    // Verificar si el directorio existe, si no, crearlo
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // Información del archivo subido
    $nombreArchivo = $file['name'];
    $tipoArchivo = $file['type'];
    $tmpArchivo = $file['tmp_name'];
    $tamañoArchivo = $file['size'];

    // Definir las extensiones permitidas (JPG, PNG, GIF y WebP)
    $extensionesPermitidas = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // Comprobar si la extensión es válida
    if (in_array($tipoArchivo, $extensionesPermitidas)) {
        // Verificar el tamaño máximo permitido (por ejemplo, 5MB)
        $tamañoMaximo = 5 * 1024 * 1024; // 5MB en bytes
        if ($tamañoArchivo <= $tamañoMaximo) {
            // Crear un nombre único aleatorio de 10 caracteres (mayúsculas y números)
            $nombreArchivoUnico = strtoupper(bin2hex(random_bytes(5))); // 10 caracteres alfanuméricos
            $ext = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
            $rutaDestino = $directorio . "/" . $nombreArchivoUnico . '.' . $ext;

            // Mover el archivo a la carpeta destino
            if (move_uploaded_file($tmpArchivo, $rutaDestino)) {
                return [
                    'status' => 'success',
                    'mensaje' => '¡La imagen se subió correctamente!',
                    'ruta' => $rutaDestino
                ];
            } else {
                return [
                    'status' => 'error',
                    'mensaje' => 'Hubo un error al subir la imagen. Intenta de nuevo.'
                ];
            }
        } else {
            return [
                'status' => 'error',
                'mensaje' => 'El archivo excede el tamaño máximo permitido de 5MB.'
            ];
        }
    } else {
        return [
            'status' => 'error',
            'mensaje' => 'Solo se permiten imágenes en formato JPG, PNG, GIF y WebP.'
        ];
    }
}
?>
