<?php

/**
 * Sube una imagen al directorio /content/uploads/{fecha}/.
 * Valida el tipo MIME real con finfo (ignora el MIME enviado por el cliente).
 *
 * @param  string     $inputName   Nombre del campo en $_FILES
 * @param  array|null $allowedMimes  ['image/jpeg' => '.jpg', ...]  (null = JPG/PNG/WEBP)
 * @return array  ['imagen_url' => '/content/...'] | ['error' => '...']
 */
function uploadImageFile(string $inputName, array $allowedMimes = null): array
{
    if ($allowedMimes === null) {
        $allowedMimes = [
            'image/jpeg' => '.jpg',
            'image/png'  => '.png',
            'image/webp' => '.webp',
        ];
    }

    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'No se subió ninguna imagen o se produjo un error.'];
    }

    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES[$inputName]['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowedMimes[$mimeType])) {
        return ['error' => 'Tipo de archivo no permitido. Solo se permiten JPG, PNG y WEBP.'];
    }

    $extension = $allowedMimes[$mimeType];
    $today     = date('Y-m-d');
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/content/uploads/' . $today . '/';

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $newFilename = bin2hex(random_bytes(8)) . $extension;

    if (!move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetDir . $newFilename)) {
        return ['error' => 'Error al subir la imagen.'];
    }

    return ['imagen_url' => '/content/uploads/' . $today . '/' . $newFilename];
}
