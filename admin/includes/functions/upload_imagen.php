<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {

    $allowedMimes = [
        'image/jpeg' => '.jpg',
        'image/png'  => '.png',
        'image/gif'  => '.gif',
        'image/webp' => '.webp',
    ];

    $result = uploadImageFile('file', $allowedMimes);

    if (isset($result['imagen_url'])) {
        echo "success";
    } else {
        echo $result['error'];
    }
} else {
    echo "Solicitud inválida";
}
?>
