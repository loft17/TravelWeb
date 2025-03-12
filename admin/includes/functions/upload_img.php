<!-- upload_img.php -->
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';

function procesarSubidaImagen($file) {
    $fecha = date("Y-m-d");
    $directorio = "../../content/uploads/" . $fecha;

    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $nombreArchivo = $file['name'];
    $tipoArchivo = $file['type'];
    $tmpArchivo = $file['tmp_name'];
    $tamañoArchivo = $file['size'];

    $extensionesPermitidas = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (in_array($tipoArchivo, $extensionesPermitidas)) {
        $tamañoMaximo = 5 * 1024 * 1024;
        if ($tamañoArchivo <= $tamañoMaximo) {
            $nombreArchivoUnico = strtoupper(bin2hex(random_bytes(5)));
            $ext = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
            $rutaDestino = $directorio . "/" . $nombreArchivoUnico . '.' . $ext;

            if (move_uploaded_file($tmpArchivo, $rutaDestino)) {
                $urlImagen = "/content/uploads/$fecha/" . $nombreArchivoUnico . '.' . $ext;
                return [
                    'status' => 'success',
                    'mensaje' => '¡La imagen se subió correctamente!',
                    'ruta' => $urlImagen
                ];
            } else {
                return [
                    'status' => 'error',
                    'mensaje' => 'Hubo un error al mover el archivo a la carpeta destino.'
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagen'])) {
    $respuesta = procesarSubidaImagen($_FILES['imagen']);
    echo json_encode($respuesta);
    exit();
}
?>
