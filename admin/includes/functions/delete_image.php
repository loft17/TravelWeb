<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['image'])) {

    $uploadsBase = realpath($_SERVER['DOCUMENT_ROOT'] . '/content/uploads');
    $imagePath   = realpath($_SERVER['DOCUMENT_ROOT'] . $_POST['image']);

    // Rechazar rutas fuera de /content/uploads/ (path traversal)
    if ($imagePath === false || $uploadsBase === false || strpos($imagePath, $uploadsBase . DIRECTORY_SEPARATOR) !== 0) {
        http_response_code(400);
        echo "error";
        exit;
    }

    if (file_exists($imagePath)) {
        echo unlink($imagePath) ? "success" : "error";
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
