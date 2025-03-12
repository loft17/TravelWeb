<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';

function obtenerCategoriasEmojis($jsonFile) {
    $categorias = [];

    if (file_exists($jsonFile)) {
        $jsonData = file_get_contents($jsonFile);
        $categorias = json_decode($jsonData, true);
    }

    return $categorias;
}
?>
